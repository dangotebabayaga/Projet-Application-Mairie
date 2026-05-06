import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { EventService, EventItem, EventPayload } from '../../services/event.service';

interface ThemeFilter {
  id: string;
  label: string;
  colorClass: string;
}

type DateRangeId = 'all' | 'upcoming' | 'today' | 'week' | 'month';

interface DateRangeFilter {
  id: DateRangeId;
  label: string;
}

interface NewEventForm {
  titre: string;
  lieux: string;
  commentaire: string;
  dateEv: string;
  heureDeb: string;
  heureFin: string;
  type: string;
  newType: string;
}

const FAVORITES_STORAGE_KEY = 'eventFavorites';

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './events.component.html',
  styleUrls: ['./events.component.scss']
})
export class EventsComponent implements OnInit {
  selectedTheme: string = 'all';
  selectedDateRange: DateRangeId = 'upcoming';
  savedEvents: string[] = [];
  events: EventItem[] = [];
  userRole: string;

  showCreateForm = false;
  submitting = false;
  formError: string | null = null;

  form: NewEventForm = this.emptyForm();

  themes: ThemeFilter[] = [
    { id: 'all', label: 'Tous', colorClass: 'theme-all' }
  ];

  dateRanges: DateRangeFilter[] = [
    { id: 'upcoming', label: 'À venir' },
    { id: 'today', label: "Aujourd'hui" },
    { id: 'week', label: 'Cette semaine' },
    { id: 'month', label: 'Ce mois-ci' },
    { id: 'all', label: 'Tous' }
  ];

  constructor(private eventService: EventService, private router: Router) {
    this.userRole = localStorage.getItem('userRole') || 'citoyen';
  }

  openEvent(event: EventItem): void {
    if (event.id) this.router.navigate(['/events', event.id]);
  }

  get isAdmin(): boolean {
    return this.userRole === 'admin';
  }

  ngOnInit(): void {
    this.loadFavorites();
    this.loadEvents();
    this.loadTypes();
  }

  loadEvents(): void {
    this.eventService.getAll().subscribe({
      next: (data: EventItem[]) => this.events = data,
      error: (err: any) => console.error('Erreur chargement événements', err)
    });
  }

  loadTypes(): void {
    this.eventService.getTypes().subscribe({
      next: (types: { type: string }[]) => {
        this.themes = [
          { id: 'all', label: 'Tous', colorClass: 'theme-all' },
          ...types.map(t => ({
            id: t.type,
            label: t.type,
            colorClass: 'theme-' + (t.type || '').toLowerCase().replace(/\s+/g, '-')
          }))
        ];
      },
      error: (err: any) => console.error('Erreur chargement types', err)
    });
  }

  get filteredEvents(): EventItem[] {
    return this.events
      .filter(e => this.selectedTheme === 'all' || e.type === this.selectedTheme)
      .filter(e => this.matchDateRange(e['date Evenement']))
      .sort((a, b) => (a['date Evenement'] || '').localeCompare(b['date Evenement'] || ''));
  }

  selectTheme(theme: string): void {
    this.selectedTheme = theme;
  }

  isThemeSelected(theme: string): boolean {
    return this.selectedTheme === theme;
  }

  selectDateRange(range: DateRangeId): void {
    this.selectedDateRange = range;
  }

  isDateRangeSelected(range: DateRangeId): boolean {
    return this.selectedDateRange === range;
  }

  private matchDateRange(eventDateStr: string | undefined): boolean {
    if (this.selectedDateRange === 'all') return true;
    if (!eventDateStr) return false;

    const eventDate = new Date(eventDateStr + 'T00:00:00');
    if (isNaN(eventDate.getTime())) return false;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    switch (this.selectedDateRange) {
      case 'upcoming':
        return eventDate.getTime() >= today.getTime();
      case 'today':
        return eventDate.getTime() === today.getTime();
      case 'week': {
        const weekEnd = new Date(today);
        weekEnd.setDate(today.getDate() + 7);
        return eventDate.getTime() >= today.getTime() && eventDate.getTime() < weekEnd.getTime();
      }
      case 'month':
        return eventDate.getMonth() === today.getMonth()
            && eventDate.getFullYear() === today.getFullYear();
      default:
        return true;
    }
  }

  private favoriteKey(event: EventItem): string {
    return `${event.titre}|${event['date Evenement']}|${event['Heure début']}`;
  }

  private loadFavorites(): void {
    const raw = localStorage.getItem(FAVORITES_STORAGE_KEY);
    this.savedEvents = raw ? JSON.parse(raw) : [];
  }

  private persistFavorites(): void {
    localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(this.savedEvents));
  }

  toggleSaveEvent(event: EventItem): void {
    const key = this.favoriteKey(event);
    if (this.savedEvents.includes(key)) {
      this.savedEvents = this.savedEvents.filter(k => k !== key);
    } else {
      this.savedEvents = [...this.savedEvents, key];
    }
    this.persistFavorites();
  }

  isEventSaved(event: EventItem): boolean {
    return this.savedEvents.includes(this.favoriteKey(event));
  }

  getThemeClass(type: string): string {
    return 'theme-' + (type || '').toLowerCase().replace(/\s+/g, '-');
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

  // ---- Création (admin) ----

  toggleCreateForm(): void {
    this.showCreateForm = !this.showCreateForm;
    if (this.showCreateForm) {
      this.form = this.emptyForm();
      this.formError = null;
    }
  }

  submitCreate(): void {
    this.formError = null;
    const adminId = Number(localStorage.getItem('userId'));
    if (!adminId) {
      this.formError = 'Vous devez être connecté.';
      return;
    }

    const typeValue = this.form.type === '__new__' ? this.form.newType.trim() : this.form.type;
    if (!this.form.titre.trim() || !typeValue) {
      this.formError = 'Le titre et le type sont obligatoires.';
      return;
    }

    const payload: EventPayload = {
      titre: this.form.titre.trim(),
      lieux: this.form.lieux.trim(),
      commentaire: this.form.commentaire.trim(),
      'date Evenement': this.form.dateEv,
      'Heure début': this.form.heureDeb,
      'Heure fin': this.form.heureFin,
      adminId,
      type: typeValue
    };

    this.submitting = true;
    this.eventService.create(payload).subscribe({
      next: () => {
        this.submitting = false;
        this.showCreateForm = false;
        this.form = this.emptyForm();
        this.loadEvents();
        this.loadTypes();
      },
      error: (err: any) => {
        this.submitting = false;
        this.formError = err?.error?.error || 'Erreur lors de la création';
        console.error('Erreur création événement', err);
      }
    });
  }

  private emptyForm(): NewEventForm {
    return {
      titre: '',
      lieux: '',
      commentaire: '',
      dateEv: '',
      heureDeb: '',
      heureFin: '',
      type: '',
      newType: ''
    };
  }

  // ---- Ajout à Google Calendar ----

  addToCalendar(event: EventItem): void {
    const url = this.buildGoogleCalendarUrl(event);
    window.open(url, '_blank', 'noopener');
  }

  private buildGoogleCalendarUrl(event: EventItem): string {
    const date = (event['date Evenement'] || '').replace(/-/g, '');
    const start = (event['Heure début'] || '00:00').replace(':', '') + '00';
    const end = (event['Heure fin'] || '23:59').replace(':', '') + '00';
    const dates = date ? `${date}T${start}/${date}T${end}` : '';

    const params = new URLSearchParams({
      action: 'TEMPLATE',
      text: event.titre || '',
      details: event.commentaire || '',
      location: event.lieux || '',
      ctz: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Europe/Paris'
    });
    if (dates) params.set('dates', dates);

    return `https://calendar.google.com/calendar/render?${params.toString()}`;
  }
}
