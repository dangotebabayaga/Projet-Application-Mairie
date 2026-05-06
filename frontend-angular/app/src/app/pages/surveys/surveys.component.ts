import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
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
  loading = true;
  error = '';

  selectedStatus: SurveyStatus = 'all';
  sortDesc = true;

  statusFilters: { id: SurveyStatus; label: string }[] = [
    { id: 'all', label: 'Tous' },
    { id: 'available', label: 'À voter' },
    { id: 'voted', label: 'Déjà votés' },
    { id: 'closed', label: 'Terminés' }
  ];

  constructor(private surveyService: SurveyService, private router: Router) {}

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
    return survey.hasVoted;
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

  openSurvey(survey: Survey): void {
    this.router.navigate(['/surveys', survey.id]);
  }

  formatDate(date: string): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
