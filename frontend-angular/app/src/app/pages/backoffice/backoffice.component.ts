import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { HttpClient } from '@angular/common/http';
import { SurveyService, Survey } from '../../services/survey.service';
import { SocialService, ReseauSocial } from '../../services/social.service';
import { ReportService, Report } from '../../services/report.service';

type EventTheme = 'sport' | 'culture' | 'citoyennete' | 'environnement';
type TabId = 'overview' | 'reports' | 'surveys' | 'events' | 'social';

interface SocialForm {
  plateform: string;
  lien: string;
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

  socials: ReseauSocial[] = [];
  socialForm: SocialForm = { plateform: '', lien: '' };
  socialLoading = false;
  socialError = '';
  socialSuccess = '';

  reports: Report[] = [];

  constructor(
    private surveyService: SurveyService,
    private socialService: SocialService,
    private reportService: ReportService,
    private http: HttpClient
  ) {}

  ngOnInit(): void {
    this.loadSurveys();
    this.loadSocials();
    this.loadReports();
  }

  loadReports(): void {
    this.reportService.getAll().subscribe({
      next: (data) => this.reports = data,
      error: (err) => console.error('Erreur chargement signalements', err)
    });
  }

  advanceReportState(id: number): void {
    this.reportService.advanceState(id).subscribe({
      next: () => this.loadReports(),
      error: (err) => console.error('Erreur changement état', err)
    });
  }

  loadSocials(): void {
    this.socialService.getAll().subscribe({
      next: (data) => this.socials = data,
      error: (err) => console.error('Erreur chargement réseaux sociaux', err)
    });
  }

  onCreateSocial(): void {
    this.socialError = '';
    this.socialSuccess = '';
    if (!this.socialForm.plateform.trim() || !this.socialForm.lien.trim()) {
      this.socialError = 'Plateforme et lien obligatoires';
      return;
    }

    this.socialLoading = true;
    this.socialService.create({
      plateform: this.socialForm.plateform.trim(),
      lien: this.socialForm.lien.trim(),
      villeId: 1
    }).subscribe({
      next: () => {
        this.socialLoading = false;
        this.socialSuccess = 'Réseau social ajouté';
        this.socialForm = { plateform: '', lien: '' };
        this.loadSocials();
      },
      error: (err) => {
        this.socialLoading = false;
        this.socialError = err.error?.error || 'Erreur lors de la création';
      }
    });
  }

  onDeleteSocial(id: number): void {
    this.socialService.delete(id).subscribe({
      next: () => this.loadSocials(),
      error: (err) => console.error('Erreur suppression réseau', err)
    });
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
    { id: 'events', label: 'Événements', icon: 'calendar' },
    { id: 'social', label: 'Réseaux sociaux', icon: 'share' }
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
      reportsInProgress: this.reports.filter(r => r.etat === 'en cours').length,
      reportsRegistered: this.reports.filter(r => r.etat === 'enregistré').length,
      surveysActive: this.surveys.length,
      surveyResponses: 0,
      eventsUpcoming: this.events.filter(e => new Date(e.date) > new Date()).length
    };
  }

  setActiveTab(tabId: TabId): void {
    this.activeTab = tabId;
  }

  getStatusConfig(etat: string) {
    const configs: Record<string, { colorClass: string; label: string }> = {
      'enregistré': { colorClass: 'status-registered', label: 'Enregistré' },
      'en cours': { colorClass: 'status-in-progress', label: 'En cours' },
      'résolu': { colorClass: 'status-resolved', label: 'Résolu' }
    };
    return configs[etat] || { colorClass: 'status-registered', label: etat };
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
