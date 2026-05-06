import { Component, OnInit, ViewChild, HostListener, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { HttpClient } from '@angular/common/http';
import { ActivatedRoute, Router } from '@angular/router';
import { SurveyService, Survey } from '../../services/survey.service';
import { SocialService, ReseauSocial } from '../../services/social.service';
import { ReportService, Report } from '../../services/report.service';
import { EventService, EventItem, EventType } from '../../services/event.service';
import { QuartierService, Quartier } from '../../services/quartier.service';
import { CategorieService, Categorie } from '../../services/categorie.service';
import { PhotoUploadComponent } from '../../components/photo-upload/photo-upload.component';

type TabId = 'overview' | 'reports' | 'surveys' | 'events' | 'social';

interface SocialForm {
  plateform: string;
  lien: string;
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
  quartiers: number[];
  categories: number[];
  multiChoice: boolean;
}

interface EventForm {
  titre: string;
  commentaire: string;
  dateEv: string;
  heureDeb: string;
  heureFin: string;
  lieux: string;
  type: string;
  newType: string;
}

@Component({
  selector: 'app-backoffice',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule, PhotoUploadComponent],
  templateUrl: './backoffice.component.html',
  styleUrls: ['./backoffice.component.scss']
})
export class BackofficeComponent implements OnInit {
  @ViewChild('eventPhotoUpload') eventPhotoUpload?: PhotoUploadComponent;
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

  events: EventItem[] = [];
  eventTypes: EventType[] = [];
  eventLoading = false;
  eventError = '';
  eventSuccess = '';

  quartiers: Quartier[] = [];
  categories: Categorie[] = [];

  showQuartierDropdown = false;
  showCategorieDropdown = false;

  selectedEventPhoto: File | null = null;
  editingEventId: number | null = null;

  private validTabs: TabId[] = ['overview', 'reports', 'surveys', 'events', 'social'];

  constructor(
    private surveyService: SurveyService,
    private socialService: SocialService,
    private reportService: ReportService,
    private eventService: EventService,
    private quartierService: QuartierService,
    private categorieService: CategorieService,
    private http: HttpClient,
    private el: ElementRef,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadSurveys();
    this.loadSocials();
    this.loadReports();
    this.loadEvents();
    this.loadEventTypes();
    this.quartierService.getAll().subscribe({ next: (d) => this.quartiers = d });
    this.categorieService.getAll().subscribe({ next: (d) => this.categories = d });

    // Sync tab actif avec le paramètre :tab de l'URL (gère bouton retour navigateur)
    this.route.paramMap.subscribe(params => {
      const tab = params.get('tab') as TabId | null;
      this.activeTab = (tab && this.validTabs.includes(tab)) ? tab : 'overview';
    });
  }

  toggleSurveyQuartier(id: number, checked: boolean): void {
    if (checked) {
      if (!this.surveyForm.quartiers.includes(id)) this.surveyForm.quartiers.push(id);
    } else {
      this.surveyForm.quartiers = this.surveyForm.quartiers.filter(q => q !== id);
    }
  }

  toggleSurveyCategorie(id: number, checked: boolean): void {
    if (checked) {
      if (!this.surveyForm.categories.includes(id)) this.surveyForm.categories.push(id);
    } else {
      this.surveyForm.categories = this.surveyForm.categories.filter(c => c !== id);
    }
  }

  isSurveyQuartierChecked(id: number): boolean {
    return this.surveyForm.quartiers.includes(id);
  }

  isSurveyCategorieChecked(id: number): boolean {
    return this.surveyForm.categories.includes(id);
  }

  // ---- Multi-select dropdowns ----

  toggleQuartierDropdown(event: Event): void {
    event.stopPropagation();
    this.showQuartierDropdown = !this.showQuartierDropdown;
    if (this.showQuartierDropdown) this.showCategorieDropdown = false;
  }

  toggleCategorieDropdown(event: Event): void {
    event.stopPropagation();
    this.showCategorieDropdown = !this.showCategorieDropdown;
    if (this.showCategorieDropdown) this.showQuartierDropdown = false;
  }

  get quartierLabel(): string {
    const n = this.surveyForm.quartiers.length;
    if (n === 0) return 'Tous les quartiers';
    if (n === 1) return this.quartiers.find(q => q.id === this.surveyForm.quartiers[0])?.nom || '1 quartier';
    return `${n} quartiers sélectionnés`;
  }

  get categorieLabel(): string {
    const n = this.surveyForm.categories.length;
    if (n === 0) return 'Toutes les catégories';
    if (n === 1) return this.categories.find(c => c.id === this.surveyForm.categories[0])?.libelle || '1 catégorie';
    return `${n} catégories sélectionnées`;
  }

  clearQuartiersSelection(event: Event): void {
    event.stopPropagation();
    this.surveyForm.quartiers = [];
  }

  clearCategoriesSelection(event: Event): void {
    event.stopPropagation();
    this.surveyForm.categories = [];
  }

  @HostListener('document:click', ['$event.target'])
  onDocClickDropdown(target: HTMLElement): void {
    if (this.showQuartierDropdown || this.showCategorieDropdown) {
      const wrapper = (this.el?.nativeElement as HTMLElement)?.querySelector?.('.dropdowns-container');
      if (wrapper && !wrapper.contains(target)) {
        this.showQuartierDropdown = false;
        this.showCategorieDropdown = false;
      }
    }
  }

  loadEvents(): void {
    this.eventService.getAll().subscribe({
      next: (data) => this.events = data,
      error: (err) => console.error('Erreur chargement événements', err)
    });
  }

  loadEventTypes(): void {
    this.eventService.getTypes().subscribe({
      next: (types) => this.eventTypes = types,
      error: (err) => console.error('Erreur chargement types', err)
    });
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
    choix: ['', ''],
    quartiers: [],
    categories: [],
    multiChoice: true
  };
  surveyLoading = false;
  surveyError = '';
  surveySuccess = '';

  eventForm: EventForm = this.emptyEventForm();

  get surveys(): Survey[] {
    return this.surveysList;
  }

  get stats() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return {
      reportsTotal: this.reports.length,
      reportsInProgress: this.reports.filter(r => r.etat === 'en cours').length,
      reportsRegistered: this.reports.filter(r => r.etat === 'enregistré').length,
      surveysActive: this.surveys.length,
      surveyResponses: this.surveys.reduce((sum, s) => sum + (s.nbVotants ?? 0), 0),
      eventsUpcoming: this.events.filter(e => {
        const d = e['date Evenement'] ? new Date(e['date Evenement'] + 'T00:00:00') : null;
        return d ? d.getTime() >= today.getTime() : false;
      }).length
    };
  }

  setActiveTab(tabId: TabId): void {
    // Navigation via URL pour que le bouton retour navigateur fonctionne
    if (tabId === 'overview') {
      this.router.navigate(['/backoffice']);
    } else {
      this.router.navigate(['/backoffice', tabId]);
    }
  }

  openSurvey(s: Survey): void {
    if (s?.id) this.router.navigate(['/surveys', s.id]);
  }

  getStatusConfig(etat: string) {
    const configs: Record<string, { colorClass: string; label: string }> = {
      'enregistré': { colorClass: 'status-registered', label: 'Enregistré' },
      'en cours': { colorClass: 'status-in-progress', label: 'En cours' },
      'résolu': { colorClass: 'status-resolved', label: 'Résolu' }
    };
    return configs[etat] || { colorClass: 'status-registered', label: etat };
  }

  getSurveyStatus(survey: Survey): { label: string; colorClass: string } {
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const start = survey.dateDebut ? new Date(survey.dateDebut) : null;
    const end = survey.dateFin ? new Date(survey.dateFin) : null;
    if (start && start.getTime() > now.getTime()) {
      return { label: 'À venir', colorClass: 'badge-upcoming' };
    }
    if (end && end.getTime() < now.getTime()) {
      return { label: 'Terminé', colorClass: 'badge-closed' };
    }
    return { label: 'Actif', colorClass: 'badge-active' };
  }

  toggleSurveyForm(): void {
    this.showSurveyForm = !this.showSurveyForm;
    if (!this.showSurveyForm) {
      this.resetSurveyForm();
    }
  }

  resetSurveyForm(): void {
    this.surveyForm = { titre: '', description: '', dateDebut: '', dateFin: '', choix: ['', ''], quartiers: [], categories: [], multiChoice: true };
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
      choix: choixNonVides,
      quartiers: this.surveyForm.quartiers,
      categories: this.surveyForm.categories,
      multiChoice: this.surveyForm.multiChoice
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
    this.editingEventId = null;
    if (!this.showEventForm) {
      this.resetEventForm();
    }
  }

  startEditEvent(ev: EventItem): void {
    if (!ev.id) return;
    this.editingEventId = ev.id;
    this.showEventForm = true;
    this.eventError = '';
    this.eventSuccess = '';
    this.eventForm = {
      titre: ev.titre || '',
      commentaire: ev.commentaire || '',
      dateEv: ev['date Evenement'] || '',
      heureDeb: ev['Heure début'] || '',
      heureFin: ev['Heure fin'] || '',
      lieux: ev.lieux || '',
      type: ev.type || '',
      newType: ''
    };
    this.selectedEventPhoto = null;
    this.eventPhotoUpload?.reset();
    setTimeout(() => {
      const el = document.querySelector('.tab-content .form-card');
      el?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 50);
  }

  get isEditingEvent(): boolean {
    return this.editingEventId !== null;
  }

  private emptyEventForm(): EventForm {
    return {
      titre: '',
      commentaire: '',
      dateEv: '',
      heureDeb: '',
      heureFin: '',
      lieux: '',
      type: '',
      newType: ''
    };
  }

  resetEventForm(): void {
    this.eventForm = this.emptyEventForm();
    this.eventError = '';
    this.eventSuccess = '';
    this.selectedEventPhoto = null;
    this.editingEventId = null;
    this.eventPhotoUpload?.reset();
  }

  onEventPhotoSelected(file: File | null): void {
    this.selectedEventPhoto = file;
  }

  onCreateEvent(): void {
    this.eventError = '';
    this.eventSuccess = '';

    const typeValue = this.eventForm.type === '__new__'
      ? this.eventForm.newType.trim()
      : this.eventForm.type;

    if (!this.eventForm.titre.trim() || !typeValue) {
      this.eventError = 'Le titre et le type sont obligatoires';
      return;
    }

    const adminId = Number(localStorage.getItem('userId'));
    if (!adminId) {
      this.eventError = 'Vous devez être connecté';
      return;
    }

    const payload = {
      titre: this.eventForm.titre.trim(),
      lieux: this.eventForm.lieux.trim(),
      commentaire: this.eventForm.commentaire.trim(),
      'date Evenement': this.eventForm.dateEv,
      'Heure début': this.eventForm.heureDeb,
      'Heure fin': this.eventForm.heureFin,
      adminId,
      type: typeValue,
      photo: this.selectedEventPhoto
    };

    this.eventLoading = true;
    const obs$ = this.editingEventId
      ? this.eventService.update(this.editingEventId, payload)
      : this.eventService.create(payload);

    const successMsg = this.editingEventId ? 'Événement mis à jour' : 'Événement créé avec succès';

    obs$.subscribe({
      next: () => {
        this.eventLoading = false;
        this.eventSuccess = successMsg;
        this.resetEventForm();
        this.loadEvents();
        this.loadEventTypes();
        setTimeout(() => {
          this.showEventForm = false;
          this.eventSuccess = '';
        }, 1500);
      },
      error: (err) => {
        this.eventLoading = false;
        this.eventError = err.error?.error || 'Erreur lors de l\'enregistrement';
      }
    });
  }

  formatDate(date: Date | string): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
