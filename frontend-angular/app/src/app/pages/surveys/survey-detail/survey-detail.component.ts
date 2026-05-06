import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { SurveyService, Survey } from '../../../services/survey.service';

@Component({
  selector: 'app-survey-detail',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, HttpClientModule],
  templateUrl: './survey-detail.component.html',
  styleUrls: ['./survey-detail.component.scss']
})
export class SurveyDetailComponent implements OnInit {
  survey: Survey | null = null;
  selectedChoixIds: number[] = [];
  submitted = false;
  loading = true;
  error = '';
  voteError = '';
  results: any[] = [];
  resultsLoading = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private surveyService: SurveyService
  ) {}

  get isCitoyen(): boolean {
    return (localStorage.getItem('userRole') || 'citoyen') === 'citoyen';
  }

  get isAdmin(): boolean {
    const role = localStorage.getItem('userRole');
    return role === 'admin' || role === 'superadmin';
  }

  /** Total des votants (somme des nb_votes pour les sondages à choix unique,
   *  ou nbVotants direct si dispo via API). */
  get totalVotants(): number {
    if (this.survey?.nbVotants !== undefined && this.survey.nbVotants !== null) {
      return this.survey.nbVotants;
    }
    // fallback : somme par défaut si pas dispo
    return this.results.reduce((sum: number, r: any) => sum + Number(r.nb_votes), 0);
  }

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) {
      this.router.navigate(['/surveys']);
      return;
    }

    this.surveyService.getAll().subscribe({
      next: (data) => {
        const found = data.find(s => s.id === id);
        if (!found) {
          this.error = 'Sondage introuvable';
          this.loading = false;
          return;
        }
        this.survey = found;
        this.loading = false;
        // Charger les résultats si l'utilisateur a voté OU s'il est admin (consultation)
        if (this.survey.hasVoted || this.isAdmin) {
          this.loadResults();
        }
      },
      error: () => {
        this.error = 'Impossible de charger le sondage';
        this.loading = false;
      }
    });
  }

  back(): void {
    if (this.isAdmin) {
      this.router.navigate(['/backoffice', 'surveys']);
    } else {
      this.router.navigate(['/surveys']);
    }
  }

  loadResults(): void {
    if (!this.survey) return;
    this.resultsLoading = true;
    this.surveyService.getResultat(this.survey.id).subscribe({
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
    // Choix unique : remplace la sélection
    if (this.survey && this.survey.multiChoice === false) {
      this.selectedChoixIds = [choixId];
      return;
    }
    // Choix multiple : toggle
    const i = this.selectedChoixIds.indexOf(choixId);
    if (i > -1) this.selectedChoixIds.splice(i, 1);
    else this.selectedChoixIds.push(choixId);
  }

  isChoixSelected(choixId: number): boolean {
    return this.selectedChoixIds.includes(choixId);
  }

  get hasVotedView(): boolean {
    return !!this.survey && (this.survey.hasVoted || this.submitted);
  }

  /** Voir les résultats : citoyen ayant voté OU admin (consultation) */
  get showResults(): boolean {
    return this.hasVotedView || this.isAdmin;
  }

  onSubmit(): void {
    if (!this.survey || this.selectedChoixIds.length === 0) {
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
      sondageId: this.survey.id,
      choixIds: this.selectedChoixIds
    }).subscribe({
      next: () => {
        this.submitted = true;
        this.voteError = '';
        this.loadResults();
      },
      error: (err) => {
        this.voteError = err.error?.error || 'Erreur lors du vote';
      }
    });
  }

  formatDate(date: string): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR');
  }
}
