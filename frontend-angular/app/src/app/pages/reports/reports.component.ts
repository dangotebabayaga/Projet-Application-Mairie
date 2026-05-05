import { Component, OnInit, AfterViewInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ReportService, Report } from '../../services/report.service';
import { TypeSignalementService, TypeSignalement } from '../../services/type-signalement.service';

declare const L: any;

interface ReportForm {
  typeId: number | null;
  description: string;
  address: string;
}

interface StatusInfo {
  label: string;
  colorClass: string;
  icon: string;
}

@Component({
  selector: 'app-reports',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './reports.component.html',
  styleUrls: ['./reports.component.scss']
})
export class ReportsComponent implements OnInit, AfterViewInit, OnDestroy {
  showForm = false;
  reports: Report[] = [];
  types: TypeSignalement[] = [];
  currentUserId: number;
  userRole: string;

  selectedFile: File | null = null;
  photoPreview: string | null = null;
  uploadError: string | null = null;
  submitting = false;

  showMap = false;

  selectedStatus: 'all' | 'enregistré' | 'en cours' | 'résolu' = 'all';
  sortDesc = true;

  statusFilters: { id: 'all' | 'enregistré' | 'en cours' | 'résolu'; label: string }[] = [
    { id: 'all', label: 'Tous' },
    { id: 'enregistré', label: 'Enregistrés' },
    { id: 'en cours', label: 'En cours' },
    { id: 'résolu', label: 'Résolus' }
  ];

  formData: ReportForm = {
    typeId: null,
    description: '',
    address: ''
  };

  private map: any = null;
  private markers: any[] = [];

  constructor(
    private reportService: ReportService,
    private typeService: TypeSignalementService
  ) {
    this.currentUserId = Number(localStorage.getItem('userId')) || 0;
    this.userRole = localStorage.getItem('userRole') || 'citoyen';
  }

  get isAdmin(): boolean {
    return this.userRole === 'admin';
  }

  ngOnInit(): void {
    this.loadTypes();
    this.loadReports();
  }

  ngAfterViewInit(): void {
    // La carte est masquée par défaut, init différée au premier toggle
  }

  ngOnDestroy(): void {
    if (this.map) {
      this.map.remove();
      this.map = null;
    }
  }

  toggleMap(): void {
    this.showMap = !this.showMap;
    if (this.showMap) {
      setTimeout(() => this.initMap(), 100);
    } else if (this.map) {
      this.map.remove();
      this.map = null;
      this.markers = [];
    }
  }

  private initMap(): void {
    if (typeof L === 'undefined' || this.map) return;
    const mapEl = document.getElementById('reports-map');
    if (!mapEl) return;

    this.map = L.map('reports-map').setView([46.603354, 1.888334], 6); // centre France
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap',
      maxZoom: 19
    }).addTo(this.map);

    this.refreshMarkers();
  }

  private refreshMarkers(): void {
    if (!this.map || typeof L === 'undefined') return;
    this.markers.forEach(m => this.map.removeLayer(m));
    this.markers = [];

    const geoReports = this.reports.filter(r => r.latitude !== null && r.longitude !== null);
    geoReports.forEach(r => {
      const marker = L.marker([r.latitude, r.longitude]).addTo(this.map);
      const status = this.getStatusInfo(r.etat).label;
      marker.bindPopup(
        `<strong>${this.escape(r.typeNom || r.titre)}</strong><br>` +
        `<em>${this.escape(status)}</em><br>` +
        `${this.escape(r.description || '')}`
      );
      this.markers.push(marker);
    });

    if (geoReports.length > 0) {
      const bounds = L.latLngBounds(geoReports.map(r => [r.latitude!, r.longitude!]));
      this.map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
    }
  }

  private escape(s: string): string {
    return (s || '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!));
  }

  loadTypes(): void {
    this.typeService.getAll().subscribe({
      next: (data) => {
        this.types = data;
        if (data.length > 0 && this.formData.typeId === null) {
          this.formData.typeId = data[0].id;
        }
      },
      error: (err) => console.error('Erreur chargement types', err)
    });
  }

  loadReports(): void {
    this.reportService.getAll().subscribe({
      next: (data) => {
        this.reports = data;
        this.refreshMarkers();
      },
      error: (err) => console.error('Erreur chargement signalements', err)
    });
  }

  get myReports(): Report[] {
    return this.applyHistoryFilters(this.reports.filter(r => r.citoyenId === this.currentUserId));
  }

  get otherReports(): Report[] {
    return this.reports.filter(r => r.citoyenId !== this.currentUserId);
  }

  private applyHistoryFilters(list: Report[]): Report[] {
    const filtered = this.selectedStatus === 'all'
      ? list
      : list.filter(r => r.etat === this.selectedStatus);

    return [...filtered].sort((a, b) => {
      const da = new Date(a.dateCrea).getTime();
      const db = new Date(b.dateCrea).getTime();
      return this.sortDesc ? db - da : da - db;
    });
  }

  selectStatus(status: 'all' | 'enregistré' | 'en cours' | 'résolu'): void {
    this.selectedStatus = status;
  }

  toggleSort(): void {
    this.sortDesc = !this.sortDesc;
  }

  getStatusInfo(etat: string): StatusInfo {
    switch (etat) {
      case 'enregistré':
        return { label: 'Enregistré', colorClass: 'status-registered', icon: 'clock' };
      case 'en cours':
        return { label: 'En cours', colorClass: 'status-in-progress', icon: 'alert-circle' };
      case 'résolu':
        return { label: 'Résolu', colorClass: 'status-resolved', icon: 'check-circle' };
      default:
        return { label: etat, colorClass: 'status-registered', icon: 'clock' };
    }
  }

  toggleForm(): void {
    this.showForm = !this.showForm;
    if (!this.showForm) {
      this.resetForm();
    }
  }

  resetForm(): void {
    this.formData = {
      typeId: this.types.length > 0 ? this.types[0].id : null,
      description: '',
      address: ''
    };
    this.selectedFile = null;
    this.photoPreview = null;
    this.uploadError = null;
  }

  onFileChange(event: Event): void {
    this.uploadError = null;
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] || null;
    if (!file) {
      this.selectedFile = null;
      this.photoPreview = null;
      return;
    }
    if (!file.type.startsWith('image/')) {
      this.uploadError = 'Le fichier doit être une image';
      this.selectedFile = null;
      this.photoPreview = null;
      input.value = '';
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      this.uploadError = 'Image trop volumineuse (max 5 Mo)';
      this.selectedFile = null;
      this.photoPreview = null;
      input.value = '';
      return;
    }
    this.selectedFile = file;
    const reader = new FileReader();
    reader.onload = e => this.photoPreview = e.target?.result as string;
    reader.readAsDataURL(file);
  }

  removePhoto(): void {
    this.selectedFile = null;
    this.photoPreview = null;
  }

  onSubmit(): void {
    if (!this.formData.description || !this.formData.address || this.formData.typeId === null) return;

    const typeNom = this.types.find(t => t.id === this.formData.typeId)?.nom || 'Signalement';

    this.submitting = true;
    this.reportService.create({
      titre: typeNom,
      description: this.formData.description,
      adresse: this.formData.address,
      typeId: this.formData.typeId,
      photo: this.selectedFile
    }).subscribe({
      next: () => {
        this.submitting = false;
        this.showForm = false;
        this.resetForm();
        this.loadReports();
      },
      error: (err) => {
        this.submitting = false;
        this.uploadError = err.error?.error || 'Erreur lors de la création';
        console.error('Erreur création signalement', err);
      }
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
