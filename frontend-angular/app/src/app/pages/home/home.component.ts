import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { Router, RouterModule } from '@angular/router';
import { SurveyService } from '../../services/survey.service';

interface User {
  name: string;
  role: 'citoyen' | 'admin';
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
  imports: [CommonModule, RouterModule, HttpClientModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  constructor(private router: Router, private surveyService: SurveyService) {}

  user: User = {
    name: '',
    role: 'citoyen'
  };

  ngOnInit(): void {
    const prenom = localStorage.getItem('userPrenom');
    const role = localStorage.getItem('userRole');
    if (prenom) {
      this.user.name = prenom;
    }
    if (role === 'admin') {
      this.user.role = 'admin';
    }
    this.loadStats();
  }

  activeSurveysCount = 0;

  loadStats(): void {
    this.surveyService.getAll().subscribe({
      next: (surveys) => {
        this.activeSurveysCount = surveys.length;
      },
      error: () => {
        this.activeSurveysCount = 0;
      }
    });
  }

  get modules(): Module[] {
    const citizenModules: Module[] = [
      {
        id: 'reports',
        title: 'Signalements',
        description: 'Signalez un problème dans votre ville',
        icon: 'file-text',
        color: 'blue',
        stats: 'Signaler un problème'
      },
      {
        id: 'surveys',
        title: 'Sondages',
        description: 'Participez aux consultations publiques',
        icon: 'bar-chart',
        color: 'purple',
        stats: `${this.activeSurveysCount} sondage(s) en cours`
      },
      {
        id: 'events',
        title: 'Agenda',
        description: 'Découvrez les événements de la ville',
        icon: 'calendar',
        color: 'green',
        stats: 'Voir les événements'
      },
      {
        id: 'discussion',
        title: 'Discussion',
        description: "Suivez l'actualité de votre mairie",
        icon: 'message-square',
        color: 'orange',
        stats: 'Voir les discussions'
      }
    ];

    if (this.user.role === 'admin') {
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
    return this.user.role === 'admin'
      ? 'Tableau de bord pour la gestion des services municipaux'
      : 'Participez à la vie de votre ville';
  }

  onNavigate(pageId: string): void {
    this.router.navigate(['/' + pageId]);
  }

  onSettings(): void {
    this.router.navigate(['/settings']);
  }
}
