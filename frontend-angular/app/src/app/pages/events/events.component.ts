import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { EventService, EventItem } from '../../services/event.service';

interface ThemeFilter {
  id: string;
  label: string;
  colorClass: string;
}

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './events.component.html',
  styleUrls: ['./events.component.scss']
})
export class EventsComponent implements OnInit {
  selectedTheme: string = 'all';
  savedEvents: string[] = [];
  events: EventItem[] = [];
  userRole: string = 'citoyen';
  roles: string[] = ['citoyen'];

  themes: ThemeFilter[] = [
    { id: 'all', label: 'Tous', colorClass: 'theme-all' }
  ];

  constructor(private eventService: EventService) {
    this.roles = JSON.parse(localStorage.getItem('userRole') || '["citoyen"]');
    this.userRole = this.roles[0];
  }

  get isadministrateur(): boolean {
      return this.roles.includes('administrateur');
  }
  
  get isElu(): boolean {
      return this.roles.includes('elu');
  }

  ngOnInit(): void {
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
            colorClass: 'theme-' + t.type.toLowerCase().replace(/\s+/g, '-')
          }))
        ];
      },
      error: (err: any) => console.error('Erreur chargement types', err)
    });
  }

  get filteredEvents(): EventItem[] {
    if (this.selectedTheme === 'all') {
      return this.events;
    }
    return this.events.filter(e => e.type === this.selectedTheme);
  }

  selectTheme(theme: string): void {
    this.selectedTheme = theme;
  }

  isThemeSelected(theme: string): boolean {
    return this.selectedTheme === theme;
  }

  toggleSaveEvent(index: number): void {
    const key = index.toString();
    if (this.savedEvents.includes(key)) {
      this.savedEvents = this.savedEvents.filter(id => id !== key);
    } else {
      this.savedEvents = [...this.savedEvents, key];
    }
  }

  isEventSaved(index: number): boolean {
    return this.savedEvents.includes(index.toString());
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
}
