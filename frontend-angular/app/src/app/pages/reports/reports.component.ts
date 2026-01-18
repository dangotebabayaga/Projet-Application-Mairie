import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

type ReportStatus = 'registered' | 'in_progress' | 'resolved';

interface ReportLocation {
  address: string;
  lat: number;
  lng: number;
}

interface Report {
  id: string;
  userId: string;
  userName: string;
  category: string;
  description: string;
  photo?: string;
  location: ReportLocation;
  status: ReportStatus;
  createdAt: Date;
  updatedAt: Date;
}

interface StatusInfo {
  label: string;
  colorClass: string;
  icon: string;
}

interface ReportForm {
  category: string;
  description: string;
  address: string;
  photo: string;
}

@Component({
  selector: 'app-reports',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './reports.component.html',
  styleUrls: ['./reports.component.scss']
})
export class ReportsComponent {
  showForm = false;

  formData: ReportForm = {
    category: 'Voirie',
    description: '',
    address: '',
    photo: ''
  };

  categories = ['Voirie', 'Éclairage', 'Propreté', 'Espaces verts', 'Mobilier urbain', 'Autre'];

  // TODO: À remplacer par AuthService
  currentUserId = 'user-1';

  // Données de démonstration
  reports: Report[] = [
    {
      id: '1',
      userId: 'user-1',
      userName: 'Jean Dupont',
      category: 'Voirie',
      description: 'Nid de poule important devant le 15 rue de la Mairie',
      location: { address: '15 rue de la Mairie', lat: 48.8566, lng: 2.3522 },
      status: 'in_progress',
      createdAt: new Date('2024-01-10'),
      updatedAt: new Date('2024-01-12')
    },
    {
      id: '2',
      userId: 'user-1',
      userName: 'Jean Dupont',
      category: 'Éclairage',
      description: 'Lampadaire défaillant, allumé en permanence',
      location: { address: 'Place de la République', lat: 48.8567, lng: 2.3523 },
      status: 'registered',
      createdAt: new Date('2024-01-14'),
      updatedAt: new Date('2024-01-14')
    },
    {
      id: '3',
      userId: 'user-2',
      userName: 'Marie Martin',
      category: 'Propreté',
      description: 'Dépôt sauvage de déchets près des containers',
      location: { address: 'Rue Victor Hugo', lat: 48.8568, lng: 2.3524 },
      status: 'resolved',
      createdAt: new Date('2024-01-08'),
      updatedAt: new Date('2024-01-11')
    },
    {
      id: '4',
      userId: 'user-3',
      userName: 'Pierre Bernard',
      category: 'Espaces verts',
      description: 'Arbre dangereux avec branche cassée',
      location: { address: 'Parc Municipal', lat: 48.8569, lng: 2.3525 },
      status: 'in_progress',
      createdAt: new Date('2024-01-12'),
      updatedAt: new Date('2024-01-13')
    }
  ];

  get myReports(): Report[] {
    return this.reports.filter(r => r.userId === this.currentUserId);
  }

  get otherReports(): Report[] {
    return this.reports.filter(r => r.userId !== this.currentUserId);
  }

  getStatusInfo(status: ReportStatus): StatusInfo {
    switch (status) {
      case 'registered':
        return { label: 'Enregistré', colorClass: 'status-registered', icon: 'clock' };
      case 'in_progress':
        return { label: 'En cours', colorClass: 'status-in-progress', icon: 'alert-circle' };
      case 'resolved':
        return { label: 'Résolu', colorClass: 'status-resolved', icon: 'check-circle' };
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

    const newReport: Report = {
      id: Date.now().toString(),
      userId: this.currentUserId,
      userName: 'Jean Dupont',
      category: this.formData.category,
      description: this.formData.description,
      photo: this.formData.photo || undefined,
      location: {
        address: this.formData.address,
        lat: 48.8566 + Math.random() * 0.01,
        lng: 2.3522 + Math.random() * 0.01
      },
      status: 'registered',
      createdAt: new Date(),
      updatedAt: new Date()
    };

    this.reports.unshift(newReport);
    this.showForm = false;
    this.resetForm();
  }

  formatDate(date: Date): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
