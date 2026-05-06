import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ReportService, Report } from '../../services/report.service';

interface ReportForm {
  category: string;
  description: string;
  address: string;
  photo: string;
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
export class ReportsComponent implements OnInit {
  showForm = false;
  reports: Report[] = [];
  utilisateurId: number | null = null; // correction : ! → null (initialisé depuis localStorage)
  userRole: string = 'citoyen';        // correction : initialisation explicite
  roles: string[] = ['citoyen'];       // correction : tableau de rôles

  formData: ReportForm = {
    category: 'Voirie',
    description: '',
    address: '',
    photo: ''
  };

  categories = ['Voirie', 'Éclairage', 'Propreté', 'Espaces verts', 'Mobilier urbain', 'Autre'];

  constructor(private reportService: ReportService) {
    this.roles = JSON.parse(localStorage.getItem('userRole') || '["citoyen"]');
    this.userRole = this.roles[0];
    const userId = localStorage.getItem('userId');
    this.utilisateurId = userId ? parseInt(userId) : null; // correction : initialisé ici
  }

  get isadministrateur(): boolean {
    return this.roles.includes('administrateur'); // correction : 'administrateur' → 'administrateur'
  }

  get isCitoyen(): boolean {
    return this.roles.includes('citoyen');
  }

  ngOnInit(): void {
    this.loadReports();
  }

  loadReports(): void {
    this.reportService.getAll().subscribe({
      next: (data) => this.reports = data,
      error: (err) => console.error('Erreur chargement signalements', err)
    });
  }

  get myReports(): Report[] {
    return this.reports.filter(r => r.utilisateurId === this.utilisateurId);
  }

  get otherReports(): Report[] {
    return this.reports.filter(r => r.utilisateurId !== this.utilisateurId); // correction : === → !==
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
      category: 'Voirie',
      description: '',
      address: '',
      photo: ''
    };
  }

  onSubmit(): void {
    if (!this.formData.description || !this.formData.address) return;
    this.reportService.create({
      titre: this.formData.category,
      description: this.formData.description,
      adresse: this.formData.address,
      etat: 'enregistré'
    }).subscribe({
      next: () => {
        this.showForm = false;
        this.resetForm();
        this.loadReports();
      },
      error: (err) => console.error('Erreur création signalement', err)
    });
  }

  formatDate(date: string): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}