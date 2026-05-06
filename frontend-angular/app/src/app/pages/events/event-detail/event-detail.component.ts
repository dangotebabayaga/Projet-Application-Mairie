import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { EventService, EventItem } from '../../../services/event.service';

const FAVORITES_STORAGE_KEY = 'eventFavorites';

@Component({
  selector: 'app-event-detail',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './event-detail.component.html',
  styleUrls: ['./event-detail.component.scss']
})
export class EventDetailComponent implements OnInit {
  event: EventItem | null = null;
  loading = true;
  error = '';
  savedEvents: string[] = [];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private eventService: EventService
  ) {}

  ngOnInit(): void {
    this.loadFavorites();

    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) {
      this.router.navigate(['/events']);
      return;
    }

    this.eventService.getAll().subscribe({
      next: (data) => {
        const found = data.find(e => e.id === id);
        if (!found) {
          this.error = 'Événement introuvable';
          this.loading = false;
          return;
        }
        this.event = found;
        this.loading = false;
      },
      error: () => {
        this.error = 'Impossible de charger l\'événement';
        this.loading = false;
      }
    });
  }

  back(): void {
    this.router.navigate(['/events']);
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

  getThemeClass(type: string): string {
    return 'theme-' + (type || '').toLowerCase().replace(/\s+/g, '-');
  }

  // Favoris (même clé que dans EventsComponent)
  private favoriteKey(event: EventItem): string {
    return `${event.titre}|${event['date Evenement']}|${event['Heure début']}`;
  }

  private loadFavorites(): void {
    const raw = localStorage.getItem(FAVORITES_STORAGE_KEY);
    this.savedEvents = raw ? JSON.parse(raw) : [];
  }

  toggleSaveEvent(): void {
    if (!this.event) return;
    const key = this.favoriteKey(this.event);
    if (this.savedEvents.includes(key)) {
      this.savedEvents = this.savedEvents.filter(k => k !== key);
    } else {
      this.savedEvents = [...this.savedEvents, key];
    }
    localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(this.savedEvents));
  }

  get isSaved(): boolean {
    if (!this.event) return false;
    return this.savedEvents.includes(this.favoriteKey(this.event));
  }

  // Google Calendar
  addToCalendar(): void {
    if (!this.event) return;
    const url = this.buildGoogleCalendarUrl(this.event);
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
