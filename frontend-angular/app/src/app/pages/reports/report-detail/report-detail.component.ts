import { Component, OnInit, AfterViewInit, OnDestroy, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ReportService, Report } from '../../../services/report.service';
import { TypeSignalementService, TypeSignalement } from '../../../services/type-signalement.service';
import { PhotoUploadComponent } from '../../../components/photo-upload/photo-upload.component';
import { ConfirmModalComponent } from '../../../components/confirm-modal/confirm-modal.component';

declare const L: any;

interface StatusInfo {
  label: string;
  colorClass: string;
}

interface EditForm {
  typeId: number | null;
  description: string;
  adresse: string;
}

@Component({
  selector: 'app-report-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, PhotoUploadComponent, ConfirmModalComponent],
  templateUrl: './report-detail.component.html',
  styleUrls: ['./report-detail.component.scss']
})
export class ReportDetailComponent implements OnInit, AfterViewInit, OnDestroy {
  @ViewChild('editPhotoUpload') editPhotoUpload?: PhotoUploadComponent;

  report: Report | null = null;
  loading = true;
  error = '';

  currentUserId: number;
  userRole: string;

  types: TypeSignalement[] = [];

  // Mode édition
  editing = false;
  editForm: EditForm = { typeId: null, description: '', adresse: '' };
  editPhoto: File | null = null;
  saving = false;
  editError = '';

  // Modal confirmation suppression
  showDeleteConfirm = false;

  private map: any = null;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private reportService: ReportService,
    private typeService: TypeSignalementService
  ) {
    this.currentUserId = Number(localStorage.getItem('userId')) || 0;
    this.userRole = localStorage.getItem('userRole') || 'citoyen';
  }

  get isAdmin(): boolean {
    return this.userRole === 'admin' || this.userRole === 'superadmin';
  }

  get isAuthor(): boolean {
    return !!this.report && this.report.citoyenId === this.currentUserId;
  }

  /** Le citoyen peut éditer/supprimer son signalement uniquement tant que l'état est "enregistré" */
  get canEditOrDelete(): boolean {
    return this.isAuthor && this.report?.etat === 'enregistré';
  }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) {
      this.router.navigate(['/reports']);
      return;
    }

    this.typeService.getAll().subscribe({
      next: (types) => this.types = types,
      error: () => this.types = []
    });

    this.loadReport(id);
  }

  private loadReport(id: number): void {
    this.reportService.getAll().subscribe({
      next: (data) => {
        const found = data.find(r => r.id === id);
        if (!found) {
          this.error = 'Signalement introuvable';
          this.loading = false;
          return;
        }
        this.report = found;
        this.loading = false;
        setTimeout(() => this.initMap(), 100);
      },
      error: () => {
        this.error = 'Impossible de charger le signalement';
        this.loading = false;
      }
    });
  }

  ngAfterViewInit(): void {}

  ngOnDestroy(): void {
    if (this.map) {
      this.map.remove();
      this.map = null;
    }
  }

  private initMap(): void {
    if (typeof L === 'undefined' || !this.report) return;
    if (this.report.latitude == null || this.report.longitude == null) return;
    const el = document.getElementById('report-detail-map');
    if (!el || this.map) return;

    const lat = Number(this.report.latitude);
    const lng = Number(this.report.longitude);

    this.map = L.map('report-detail-map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap',
      maxZoom: 19
    }).addTo(this.map);

    L.marker([lat, lng]).addTo(this.map)
      .bindPopup(`<strong>${this.escape(this.report.typeNom || this.report.titre || '')}</strong>`)
      .openPopup();
  }

  private escape(s: string): string {
    return (s || '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!));
  }

  back(): void {
    this.router.navigate(['/reports']);
  }

  getStatusInfo(etat: string): StatusInfo {
    switch (etat) {
      case 'enregistré': return { label: 'Enregistré', colorClass: 'status-registered' };
      case 'en cours':   return { label: 'En cours', colorClass: 'status-in-progress' };
      case 'résolu':     return { label: 'Résolu', colorClass: 'status-resolved' };
      default:           return { label: etat, colorClass: 'status-registered' };
    }
  }

  formatDate(date: string): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  advanceState(): void {
    if (!this.report || !this.isAdmin) return;
    this.reportService.advanceState(this.report.id).subscribe({
      next: () => {
        if (!this.report) return;
        this.reportService.getAll().subscribe(data => {
          const found = data.find(r => r.id === this.report!.id);
          if (found) this.report = found;
        });
      },
      error: (err) => alert(err.error?.error || 'Erreur changement état')
    });
  }

  // ---- Édition ----

  startEdit(): void {
    if (!this.report || !this.canEditOrDelete) return;
    this.editing = true;
    this.editError = '';
    this.editForm = {
      typeId: this.report.typeId ?? null,
      description: this.report.description || '',
      adresse: this.report.adresse || ''
    };
    this.editPhoto = null;
  }

  cancelEdit(): void {
    this.editing = false;
    this.editError = '';
    this.editPhoto = null;
    this.editPhotoUpload?.reset();
  }

  onEditPhotoSelected(file: File | null): void {
    this.editPhoto = file;
  }

  saveEdit(): void {
    if (!this.report || !this.canEditOrDelete || this.saving) return;
    if (!this.editForm.description.trim() || !this.editForm.adresse.trim() || this.editForm.typeId === null) {
      this.editError = 'Description, adresse et catégorie sont obligatoires';
      return;
    }

    const typeNom = this.types.find(t => t.id === this.editForm.typeId)?.nom || this.report.titre || 'Signalement';

    this.saving = true;
    this.editError = '';

    this.reportService.update(this.report.id, {
      titre: typeNom,
      description: this.editForm.description.trim(),
      adresse: this.editForm.adresse.trim(),
      typeId: this.editForm.typeId,
      photo: this.editPhoto
    }).subscribe({
      next: () => {
        this.saving = false;
        this.editing = false;
        // Re-fetch pour récupérer la nouvelle adresse/lat/lng/photo
        if (this.report) {
          if (this.map) {
            this.map.remove();
            this.map = null;
          }
          this.loadReport(this.report.id);
        }
      },
      error: (err) => {
        this.saving = false;
        this.editError = err.error?.error || 'Erreur lors de la mise à jour';
      }
    });
  }

  // ---- Suppression ----

  askDelete(): void {
    if (!this.report || !this.canEditOrDelete) return;
    this.showDeleteConfirm = true;
  }

  cancelDelete(): void {
    this.showDeleteConfirm = false;
  }

  confirmDelete(): void {
    if (!this.report || !this.canEditOrDelete) return;
    this.showDeleteConfirm = false;
    this.reportService.delete(this.report.id).subscribe({
      next: () => this.router.navigate(['/reports']),
      error: (err) => alert(err.error?.error || 'Erreur lors de la suppression')
    });
  }
}
