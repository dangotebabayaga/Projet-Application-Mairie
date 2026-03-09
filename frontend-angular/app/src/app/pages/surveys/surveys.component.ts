import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { SurveyService, Survey } from '../../services/survey.service';

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

  constructor(private surveyService: SurveyService) {}

  get surveys(): Survey[] {
    return this.surveyService.getAll();
  }

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
