import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

type EventTheme = 'sport' | 'culture' | 'citoyennete' | 'environnement';

interface Event {
  id: string;
  title: string;
  description: string;
  date: Date;
  endDate?: Date;
  location: string;
  theme: EventTheme;
  organizer: string;
  imageUrl?: string;
}

interface ThemeFilter {
  id: EventTheme | 'all';
  label: string;
  colorClass: string;
}

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './events.component.html',
  styleUrls: ['./events.component.scss']
})
export class EventsComponent {
  selectedTheme: EventTheme | 'all' = 'all';
  savedEvents: string[] = [];

  themes: ThemeFilter[] = [
    { id: 'all', label: 'Tous', colorClass: 'theme-all' },
    { id: 'sport', label: 'Sport', colorClass: 'theme-sport' },
    { id: 'culture', label: 'Culture', colorClass: 'theme-culture' },
    { id: 'citoyennete', label: 'Citoyenneté', colorClass: 'theme-citoyennete' },
    { id: 'environnement', label: 'Environnement', colorClass: 'theme-environnement' }
  ];

  // Données de démonstration
  events: Event[] = [
    {
      id: '1',
      title: 'Marathon de la ville',
      description: 'Course à pied ouverte à tous les niveaux. Parcours de 5km, 10km et 21km à travers les plus beaux quartiers de la ville.',
      date: new Date('2024-03-15'),
      location: 'Place de la Mairie',
      theme: 'sport',
      organizer: 'Club Athlétique Municipal',
      imageUrl: 'https://images.unsplash.com/photo-1532444458054-01a7dd3e9fca?w=400'
    },
    {
      id: '2',
      title: 'Festival des Arts',
      description: 'Exposition d\'artistes locaux, concerts et spectacles de rue pendant tout le week-end.',
      date: new Date('2024-03-20'),
      location: 'Centre Culturel',
      theme: 'culture',
      organizer: 'Association Culturelle',
      imageUrl: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400'
    },
    {
      id: '3',
      title: 'Réunion citoyenne',
      description: 'Débat public sur le budget participatif 2024. Venez proposer vos idées et voter pour vos projets préférés.',
      date: new Date('2024-02-28'),
      location: 'Salle des fêtes',
      theme: 'citoyennete',
      organizer: 'Mairie',
      imageUrl: 'https://images.unsplash.com/photo-1577563908411-5077b6dc7624?w=400'
    },
    {
      id: '4',
      title: 'Plantation d\'arbres',
      description: 'Journée de plantation participative au parc municipal. Arbres et outils fournis.',
      date: new Date('2024-03-10'),
      location: 'Parc Municipal',
      theme: 'environnement',
      organizer: 'Service Espaces Verts',
      imageUrl: 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=400'
    },
    {
      id: '5',
      title: 'Tournoi de football inter-quartiers',
      description: 'Compétition amicale entre les équipes des différents quartiers de la ville.',
      date: new Date('2024-04-05'),
      location: 'Stade Municipal',
      theme: 'sport',
      organizer: 'Association Sportive',
      imageUrl: 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=400'
    },
    {
      id: '6',
      title: 'Conférence climat',
      description: 'Intervention d\'experts sur les enjeux climatiques locaux et les actions possibles.',
      date: new Date('2024-03-25'),
      location: 'Médiathèque',
      theme: 'environnement',
      organizer: 'Collectif Éco-citoyen'
    }
  ];

  get filteredEvents(): Event[] {
    let filtered = this.selectedTheme === 'all'
      ? this.events
      : this.events.filter(e => e.theme === this.selectedTheme);

    return filtered.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
  }

  get savedEventsList(): Event[] {
    return this.events.filter(e => this.savedEvents.includes(e.id));
  }

  selectTheme(theme: EventTheme | 'all'): void {
    this.selectedTheme = theme;
  }

  isThemeSelected(theme: EventTheme | 'all'): boolean {
    return this.selectedTheme === theme;
  }

  toggleSaveEvent(eventId: string): void {
    if (this.savedEvents.includes(eventId)) {
      this.savedEvents = this.savedEvents.filter(id => id !== eventId);
    } else {
      this.savedEvents = [...this.savedEvents, eventId];
    }
  }

  isEventSaved(eventId: string): boolean {
    return this.savedEvents.includes(eventId);
  }

  addToCalendar(event: Event): void {
    alert(`"${event.title}" ajouté à votre calendrier personnel !`);
  }

  getThemeClass(theme: EventTheme): string {
    return `theme-${theme}`;
  }

  getThemeLabel(theme: EventTheme): string {
    return this.themes.find(t => t.id === theme)?.label || theme;
  }

  formatDate(date: Date): string {
    return new Date(date).toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
}
