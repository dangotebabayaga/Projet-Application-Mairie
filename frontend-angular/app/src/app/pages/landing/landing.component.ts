import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

interface Feature {
  icon: string;
  title: string;
  description: string;
}

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './landing.component.html',
  styleUrls: ['./landing.component.scss']
})
export class LandingComponent {
  constructor(private router: Router) {}

  features: Feature[] = [
    {
      icon: 'file-text',
      title: 'Signalements citoyens',
      description: 'Signalez les problèmes urbains avec photos et géolocalisation GPS'
    },
    {
      icon: 'bar-chart',
      title: 'Sondages & Consultations',
      description: 'Participez aux consultations publiques et donnez votre avis'
    },
    {
      icon: 'calendar',
      title: 'Agenda participatif',
      description: 'Découvrez tous les événements de la ville et des associations'
    },
    {
      icon: 'message-square',
      title: 'Discussion citoyenne',
      description: "Suivez l'actualité de votre mairie en temps réel"
    },
    {
      icon: 'shield',
      title: 'Back-office municipal',
      description: 'Outil de gestion pour les élus et agents municipaux'
    },
    {
      icon: 'map-pin',
      title: 'Géolocalisation',
      description: 'Filtrez par quartier et localisez précisément vos signalements'
    }
  ];

  benefits: string[] = [
    'Plateforme 100% responsive (mobile et desktop)',
    "Système d'authentification sécurisé",
    'Suivi en temps réel des signalements',
    'Historique complet et consultable',
    'Interface intuitive et moderne',
    'Données synchronisées entre citoyens et municipalité'
  ];

  onEnter(): void {
    this.router.navigate(['/login']);
  }
}
