import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { HttpClient } from '@angular/common/http';
import { SurveyService, Survey } from '../../services/survey.service';

type ReportStatus = 'registered' | 'in_progress' | 'resolved';
type EventTheme = 'sport' | 'culture' | 'citoyennete' | 'environnement';
type TabId = 'overview' | 'reports' | 'surveys' | 'events';

interface Report {
  id: string;
  userId: string;
  userName: string;
  category: string;
  description: string;
  photo?: string;
  location: { address: string; lat: number; lng: number };
  status: ReportStatus;
  createdAt: Date;
  updatedAt: Date;
}


interface Event {
  id: string;
  title: string;
  description: string;
  date: Date;
  location: string;
  theme: EventTheme;
  organizer: string;
  imageUrl?: string;
}

interface Tab {
  id: TabId;
  label: string;
  icon: string;
}

interface SurveyForm {
  titre: string;
  description: string;
  dateDebut: string;
  dateFin: string;
  choix: string[];
}

interface EventForm {
  title: string;
  description: string;
  date: string;
  location: string;
  theme: EventTheme;
  organizer: string;
  imageUrl: string;
}

@Component({
  selector: 'app-backoffice',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './backoffice.component.html',
  styleUrls: ['./backoffice.component.scss']
})
export class BackofficeComponent implements OnInit {
  activeTab: TabId = 'overview';
  showSurveyForm = false;
  showEventForm = false;
  surveysList: Survey[] = [];

  constructor(private surveyService: SurveyService, private http: HttpClient) {}

  ngOnInit(): void {
    this.loadSurveys();
  }

  loadSurveys(): void {
    this.surveyService.getAll().subscribe({
      next: (data) => this.surveysList = data,
      error: (err) => console.error('Erreur chargement sondages', err)
    });
  }

  tabs: Tab[] = [
    { id: 'overview', label: "Vue d'ensemble", icon: 'trending-up' },
    { id: 'reports', label: 'Signalements', icon: 'file-text' },
    { id: 'surveys', label: 'Sondages', icon: 'bar-chart' },
    { id: 'events', label: 'Événements', icon: 'calendar' }
  ];

  surveyForm: SurveyForm = {
    titre: '',
    description: '',
    dateDebut: '',
    dateFin: '',
    choix: ['', '']
  };
  surveyLoading = false;
  surveyError = '';
  surveySuccess = '';

  eventForm: EventForm = {
    title: '',
    description: '',
    date: '',
    location: '',
    theme: 'culture',
    organizer: 'Mairie',
    imageUrl: ''
  };

  get surveys(): Survey[] {
    return this.surveysList;
  }

  // Données de démonstration
  reports: Report[] = [
    {
      id: '1',
      userId: 'user-1',
      userName: 'Jean Dupont',
      category: 'Voirie',
      description: 'Nid de poule important devant le 15 rue de la Mairie',
      location: { address: '15 rue de la Mairie', lat: 48.8566, lng: 2.3522 },
      status: 'registered',
      createdAt: new Date('2024-01-14'),
      updatedAt: new Date('2024-01-14')
    },
    {
      id: '2',
      userId: 'user-2',
      userName: 'Marie Martin',
      category: 'Éclairage',
      description: 'Lampadaire défaillant près de l\'école',
      location: { address: 'Rue de l\'École', lat: 48.8567, lng: 2.3523 },
      status: 'in_progress',
      createdAt: new Date('2024-01-10'),
      updatedAt: new Date('2024-01-12')
    },
    {
      id: '3',
      userId: 'user-3',
      userName: 'Pierre Bernard',
      category: 'Propreté',
      description: 'Dépôt sauvage de déchets',
      location: { address: 'Rue Victor Hugo', lat: 48.8568, lng: 2.3524 },
      status: 'resolved',
      createdAt: new Date('2024-01-05'),
      updatedAt: new Date('2024-01-08')
    }
  ];

  events: Event[] = [
    {
      id: '1',
      title: 'Marathon de la ville',
      description: 'Course à pied ouverte à tous',
      date: new Date('2024-03-15'),
      location: 'Place de la Mairie',
      theme: 'sport',
      organizer: 'Club Athlétique',
      imageUrl: 'https://images.unsplash.com/photo-1532444458054-01a7dd3e9fca?w=400'
    },
    {
      id: '2',
      title: 'Festival des Arts',
      description: 'Exposition et spectacles',
      date: new Date('2024-03-20'),
      location: 'Centre Culturel',
      theme: 'culture',
      organizer: 'Association Culturelle'
    }
  ];

  get stats() {
    return {
      reportsTotal: this.reports.length,
      reportsInProgress: this.reports.filter(r => r.status === 'in_progress').length,
      reportsRegistered: this.reports.filter(r => r.status === 'registered').length,
      surveysActive: this.surveys.length,
      surveyResponses: 0,
      eventsUpcoming: this.events.filter(e => new Date(e.date) > new Date()).length
    };
  }

  setActiveTab(tabId: TabId): void {
    this.activeTab = tabId;
  }

  getStatusConfig(status: ReportStatus) {
    const configs = {
      registered: { colorClass: 'status-registered', label: 'Enregistré' },
      in_progress: { colorClass: 'status-in-progress', label: 'En cours' },
      resolved: { colorClass: 'status-resolved', label: 'Résolu' }
    };
    return configs[status];
  }

  updateReportStatus(reportId: string, newStatus: ReportStatus): void {
    const report = this.reports.find(r => r.id === reportId);
    if (report) {
      report.status = newStatus;
      report.updatedAt = new Date();
    }
  }

  toggleSurveyForm(): void {
    this.showSurveyForm = !this.showSurveyForm;
    if (!this.showSurveyForm) {
      this.resetSurveyForm();
    }
  }

  resetSurveyForm(): void {
    this.surveyForm = { titre: '', description: '', dateDebut: '', dateFin: '', choix: ['', ''] };
    this.surveyError = '';
    this.surveySuccess = '';
  }

  trackByIndex(index: number): number {
    return index;
  }

  updateChoix(index: number, value: string): void {
    this.surveyForm.choix[index] = value;
  }

  addChoix(): void {
    this.surveyForm.choix.push('');
  }

  removeChoix(index: number): void {
    this.surveyForm.choix.splice(index, 1);
  }

  onCreateSurvey(): void {
    if (!this.surveyForm.titre || !this.surveyForm.description || !this.surveyForm.dateDebut || !this.surveyForm.dateFin) {
      this.surveyError = 'Veuillez remplir tous les champs obligatoires';
      return;
    }

    const choixNonVides = this.surveyForm.choix.filter(c => c.trim() !== '');
    if (choixNonVides.length < 2) {
      this.surveyError = 'Veuillez ajouter au moins 2 choix';
      return;
    }

    const userId = localStorage.getItem('userId');
    if (!userId) {
      this.surveyError = 'Vous devez être connecté';
      return;
    }

    this.surveyLoading = true;
    this.surveyError = '';
    this.surveySuccess = '';

    const payload = {
      titre: this.surveyForm.titre,
      description: this.surveyForm.description,
      dateDebut: this.surveyForm.dateDebut,
      dateFin: this.surveyForm.dateFin,
      administrateur_Id: parseInt(userId),
      choix: choixNonVides
    };

    this.http.post('http://localhost:8000/api/sondages', payload).subscribe({
      next: () => {
        this.surveyLoading = false;
        this.surveySuccess = 'Sondage créé avec succès !';
        this.loadSurveys();
        setTimeout(() => {
          this.showSurveyForm = false;
          this.resetSurveyForm();
        }, 1500);
      },
      error: (err) => {
        this.surveyLoading = false;
        this.surveyError = err.error?.error || 'Erreur lors de la création du sondage';
      }
    });
  }

  toggleEventForm(): void {
    this.showEventForm = !this.showEventForm;
    if (!this.showEventForm) {
      this.resetEventForm();
    }
  }

  resetEventForm(): void {
    this.eventForm = {
      title: '',
      description: '',
      date: '',
      location: '',
      theme: 'culture',
      organizer: 'Mairie',
      imageUrl: ''
    };
  }

  onCreateEvent(): void {
    if (!this.eventForm.title || !this.eventForm.description || !this.eventForm.date || !this.eventForm.location) return;

    const newEvent: Event = {
      id: Date.now().toString(),
      title: this.eventForm.title,
      description: this.eventForm.description,
      date: new Date(this.eventForm.date),
      location: this.eventForm.location,
      theme: this.eventForm.theme,
      organizer: this.eventForm.organizer,
      imageUrl: this.eventForm.imageUrl || undefined
    };

    this.events.unshift(newEvent);
    this.showEventForm = false;
    this.resetEventForm();
  }

  formatDate(date: Date | string): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
