import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

type SurveyQuestionType = 'single' | 'multiple' | 'text';

interface SurveyQuestion {
  id: string;
  question: string;
  type: SurveyQuestionType;
  options?: string[];
}

interface Survey {
  id: string;
  title: string;
  description: string;
  questions: SurveyQuestion[];
  createdAt: Date;
  endDate: Date;
  neighborhood?: string;
  isActive: boolean;
  responses: number;
}

@Component({
  selector: 'app-surveys',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './surveys.component.html',
  styleUrls: ['./surveys.component.scss']
})
export class SurveysComponent {
  selectedSurvey: Survey | null = null;
  responses: Record<string, string | string[]> = {};
  submitted: string[] = [];

  // Données de démonstration
  surveys: Survey[] = [
    {
      id: '1',
      title: 'Aménagement du centre-ville',
      description: 'Donnez votre avis sur le projet de réaménagement de la place centrale et des rues piétonnes.',
      questions: [
        {
          id: 'q1',
          question: 'Êtes-vous favorable à la piétonnisation du centre-ville ?',
          type: 'single',
          options: ['Oui, totalement', 'Oui, partiellement', 'Non', 'Sans avis']
        },
        {
          id: 'q2',
          question: 'Quels aménagements souhaitez-vous voir ? (plusieurs choix possibles)',
          type: 'multiple',
          options: ['Plus d\'espaces verts', 'Terrasses de cafés', 'Aires de jeux', 'Bancs publics', 'Fontaines']
        },
        {
          id: 'q3',
          question: 'Avez-vous des suggestions supplémentaires ?',
          type: 'text'
        }
      ],
      createdAt: new Date('2024-01-01'),
      endDate: new Date('2024-02-28'),
      isActive: true,
      responses: 156
    },
    {
      id: '2',
      title: 'Horaires de la médiathèque',
      description: 'Participez à la consultation sur les nouveaux horaires d\'ouverture de la médiathèque municipale.',
      questions: [
        {
          id: 'q1',
          question: 'Quel créneau horaire vous convient le mieux ?',
          type: 'single',
          options: ['Matin (9h-12h)', 'Après-midi (14h-18h)', 'Soirée (18h-21h)', 'Week-end']
        },
        {
          id: 'q2',
          question: 'À quelle fréquence visitez-vous la médiathèque ?',
          type: 'single',
          options: ['Plusieurs fois par semaine', 'Une fois par semaine', 'Une fois par mois', 'Rarement']
        }
      ],
      createdAt: new Date('2024-01-10'),
      endDate: new Date('2024-01-31'),
      neighborhood: 'Centre-ville',
      isActive: true,
      responses: 89
    },
    {
      id: '3',
      title: 'Budget participatif 2024',
      description: 'Votez pour les projets que vous souhaitez voir financés par le budget participatif.',
      questions: [
        {
          id: 'q1',
          question: 'Quel projet souhaitez-vous voir réalisé en priorité ?',
          type: 'single',
          options: ['Rénovation du skate park', 'Création d\'un jardin partagé', 'Installation de bornes de recharge', 'Réfection des trottoirs']
        }
      ],
      createdAt: new Date('2024-01-05'),
      endDate: new Date('2024-03-15'),
      isActive: true,
      responses: 234
    }
  ];

  get activeSurveys(): Survey[] {
    return this.surveys.filter(s => s.isActive && !this.submitted.includes(s.id));
  }

  get completedSurveys(): Survey[] {
    return this.surveys.filter(s => this.submitted.includes(s.id));
  }

  selectSurvey(survey: Survey): void {
    this.selectedSurvey = survey;
    this.responses = {};
  }

  closeSurvey(): void {
    this.selectedSurvey = null;
    this.responses = {};
  }

  handleResponseChange(questionId: string, value: string): void {
    this.responses[questionId] = value;
  }

  toggleMultipleChoice(questionId: string, option: string): void {
    const current = (this.responses[questionId] as string[]) || [];
    if (current.includes(option)) {
      this.responses[questionId] = current.filter(o => o !== option);
    } else {
      this.responses[questionId] = [...current, option];
    }
  }

  isOptionSelected(questionId: string, option: string): boolean {
    const current = this.responses[questionId];
    if (Array.isArray(current)) {
      return current.includes(option);
    }
    return false;
  }

  onSubmit(): void {
    if (!this.selectedSurvey) return;

    this.submitted.push(this.selectedSurvey.id);
    // Ne pas fermer immédiatement pour montrer le message de confirmation
  }

  isSubmitted(surveyId: string): boolean {
    return this.submitted.includes(surveyId);
  }

  formatDate(date: Date): string {
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
