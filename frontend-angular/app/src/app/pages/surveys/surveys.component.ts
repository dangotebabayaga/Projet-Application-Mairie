import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { SurveyService, Survey } from '../../services/survey.service';

interface StatusInfo {
  label: string;
  colorClass: string;
}

type SurveyStatus = 'all' | 'available' | 'voted' | 'closed';

@Component({
  selector: 'app-surveys',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './surveys.component.html',
  styleUrls: ['./surveys.component.scss']
})
export class SurveysComponent implements OnInit {
  surveys: Survey[] = [];
  selectedSurvey: Survey | null = null;
  selectedChoixIds: number[] = [];
  submitted: number[] = [];
  loading = true;
  error = '';
  voteError = '';
  results: any[] = [];
  resultsLoading = false;

  selectedStatus: SurveyStatus = 'all';
  sortDesc = true;

  statusFilters: { id: SurveyStatus; label: string }[] = [
    { id: 'all', label: 'Tous' },
    { id: 'available', label: 'À voter' },
    { id: 'voted', label: 'Déjà votés' },
    { id: 'closed', label: 'Terminés' }
  ];

  constructor(private surveyService: SurveyService) {}

  ngOnInit(): void {
    this.loadSurveys();
  }

  loadSurveys(): void {
    this.loading = true;
    this.error = '';
    this.surveyService.getAll().subscribe({
      next: (data) => {
        this.surveys = data;
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Impossible de charger les sondages';
        this.loading = false;
        console.error(err);
      }
    });
  }

  get filteredSurveys(): Survey[] {
    const filtered = this.surveys.filter(s => {
      switch (this.selectedStatus) {
        case 'available': return !this.isVoted(s) && !this.isClosed(s);
        case 'voted':     return this.isVoted(s);
        case 'closed':    return this.isClosed(s) && !this.isVoted(s);
        default:          return true;
      }
    });

    return [...filtered].sort((a, b) => {
      const da = a.dateDebut ? new Date(a.dateDebut).getTime() : 0;
      const db = b.dateDebut ? new Date(b.dateDebut).getTime() : 0;
      return this.sortDesc ? db - da : da - db;
    });
  }

  isVoted(survey: Survey): boolean {
    return survey.hasVoted || this.submitted.includes(survey.id);
  }

  isClosed(survey: Survey): boolean {
    if (!survey.dateFin) return false;
    return new Date(survey.dateFin).getTime() < Date.now();
  }

  getStatusInfo(survey: Survey): StatusInfo {
    if (this.isVoted(survey)) return { label: 'Voté', colorClass: 'status-voted' };
    if (this.isClosed(survey)) return { label: 'Terminé', colorClass: 'status-closed' };
    return { label: 'À voter', colorClass: 'status-active' };
  }

  selectStatus(status: SurveyStatus): void {
    this.selectedStatus = status;
  }

  toggleSort(): void {
    this.sortDesc = !this.sortDesc;
  }

  selectSurvey(survey: Survey): void {
    this.selectedSurvey = survey;
    this.selectedChoixIds = [];
    this.voteError = '';
  }

  closeSurvey(): void {
    this.selectedSurvey = null;
    this.selectedChoixIds = [];
    this.voteError = '';
    this.results = [];
  }

  viewResults(survey: Survey): void {
    this.selectedSurvey = survey;
    this.loadResults(survey.id);
  }

  loadResults(sondageId: number): void {
    this.resultsLoading = true;
    this.surveyService.getResultat(sondageId).subscribe({
      next: (data) => {
        this.results = data.resultats || [];
        this.resultsLoading = false;
      },
      error: () => {
        this.results = [];
        this.resultsLoading = false;
      }
    });
  }

  getPercentage(nbVotes: number): number {
    const total = this.results.reduce((sum: number, r: any) => sum + Number(r.nb_votes), 0);
    if (total === 0) return 0;
    return Math.round((Number(nbVotes) / total) * 100);
  }

  toggleChoix(choixId: number): void {
    const index = this.selectedChoixIds.indexOf(choixId);
    if (index > -1) {
      this.selectedChoixIds.splice(index, 1);
    } else {
      this.selectedChoixIds.push(choixId);
    }
  }

  isChoixSelected(choixId: number): boolean {
    return this.selectedChoixIds.includes(choixId);
  }

  onSubmit(): void {
    if (!this.selectedSurvey || this.selectedChoixIds.length === 0) {
      this.voteError = 'Veuillez sélectionner au moins un choix';
      return;
    }

    const userId = localStorage.getItem('userId');
    if (!userId) {
      this.voteError = 'Vous devez être connecté pour voter';
      return;
    }

    this.surveyService.vote({
      citoyenId: parseInt(userId),
      sondageId: this.selectedSurvey.id,
      choixIds: this.selectedChoixIds
    }).subscribe({
      next: () => {
        this.submitted.push(this.selectedSurvey!.id);
        this.voteError = '';
        this.loadResults(this.selectedSurvey!.id);
      },
      error: (err) => {
        this.voteError = err.error?.error || 'Erreur lors du vote';
        console.error(err);
      }
    });
  }

  isSubmitted(surveyId: number): boolean {
    return this.submitted.includes(surveyId);
  }

  formatDate(date: string): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
