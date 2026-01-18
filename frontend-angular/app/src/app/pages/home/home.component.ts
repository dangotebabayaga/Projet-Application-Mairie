import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

interface User {
  name: string;
  role: 'citizen' | 'municipal';
  neighborhood?: string;
}

interface Module {
  id: string;
  title: string;
  description: string;
  icon: string;
  color: string;
  stats: string;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  @Output() navigate = new EventEmitter<string>();

  // TODO: À remplacer par AuthService et DataService
  user: User = {
    name: 'Jean Dupont',
    role: 'citizen',
    neighborhood: 'Centre-ville'
  };

  // Données de démonstration
  activeReportsCount = 12;
  activeSurveysCount = 3;
  upcomingEventsCount = 5;
  totalSurveyResponses = 234;

  get modules(): Module[] {
    const citizenModules: Module[] = [
      {
        id: 'reports',
        title: 'Signalements',
        description: 'Signalez un problème dans votre ville',
        icon: 'file-text',
        color: 'blue',
        stats: `${this.activeReportsCount} signalements actifs`
      },
      {
        id: 'surveys',
        title: 'Sondages',
        description: 'Participez aux consultations publiques',
        icon: 'bar-chart',
        color: 'purple',
        stats: `${this.activeSurveysCount} sondages en cours`
      },
      {
        id: 'events',
        title: 'Agenda',
        description: 'Découvrez les événements de la ville',
        icon: 'calendar',
        color: 'green',
        stats: `${this.upcomingEventsCount} événements à venir`
      },
      {
        id: 'discussion',
        title: 'Discussion',
        description: "Suivez l'actualité de votre mairie",
        icon: 'message-square',
        color: 'orange',
        stats: '3 nouvelles publications'
      }
    ];

    if (this.user?.role === 'municipal') {
      return [
        ...citizenModules,
        {
          id: 'backoffice',
          title: 'Back-Office',
          description: 'Gérez les signalements et les services',
          icon: 'layout-dashboard',
          color: 'red',
          stats: 'Tableau de bord'
        }
      ];
    }

    return citizenModules;
  }

  get welcomeMessage(): string {
    return this.user?.role === 'municipal'
      ? 'Tableau de bord pour la gestion des services municipaux'
      : 'Participez à la vie de votre ville';
  }

  onNavigate(pageId: string): void {
    this.navigate.emit(pageId);
  }
}
