import { Component, OnInit, HostListener, ElementRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { Router, RouterModule } from '@angular/router';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { SurveyService, Survey } from '../../services/survey.service';
import { ReportService, Report } from '../../services/report.service';
import { EventService, EventItem } from '../../services/event.service';

interface User {
  name: string;
  role: 'citoyen' | 'admin' | 'superadmin';
}

interface Module {
  id: string;
  title: string;
  description: string;
  icon: string;
  color: string;
  stats: string;
}

interface Notification {
  id: string;
  type: 'survey' | 'report' | 'event';
  title: string;
  message: string;
  route: string;
  date: Date;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, HttpClientModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  private dismissedKey = 'dismissedNotifs';

  user: User = {
    name: '',
    role: 'citoyen'
  };

  activeSurveysCount = 0;
  totalVotants = 0;
  reportsRegisteredCount = 0;   // 'enregistré' (nouveaux)
  reportsInProgressCount = 0;   // 'en cours'
  upcomingEventsCount = 0;

  /** Total des signalements à traiter (enregistré + en cours, càd tout sauf résolu) */
  get reportsActiveCount(): number {
    return this.reportsRegisteredCount + this.reportsInProgressCount;
  }

  showNotifs = false;
  notifications: Notification[] = [];
  notifsLoading = false;
  private notifsLoaded = false;

  constructor(
    private router: Router,
    private surveyService: SurveyService,
    private reportService: ReportService,
    private eventService: EventService,
    private el: ElementRef
  ) {}

  ngOnInit(): void {
    const prenom = localStorage.getItem('userPrenom');
    const role = localStorage.getItem('userRole');
    if (prenom) {
      this.user.name = prenom;
    }
    if (role === 'admin') {
      this.user.role = 'admin';
    } else if (role === 'superadmin') {
      this.user.role = 'superadmin';
    }
    this.loadStats();
    this.loadNotifications();
  }

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

  loadNotifications(): void {
    this.notifsLoading = true;
    forkJoin({
      surveys: this.surveyService.getAll().pipe(catchError(() => of([] as Survey[]))),
      reports: this.reportService.getAll().pipe(catchError(() => of([] as Report[]))),
      events: this.eventService.getAll().pipe(catchError(() => of([] as EventItem[])))
    }).subscribe({
      next: ({ surveys, reports, events }) => {
        this.notifications = this.computeNotifications(surveys, reports, events);
        this.notifsLoading = false;
        this.notifsLoaded = true;

        // Compteurs pour les indicateurs admin (alignés avec /backoffice)
        this.reportsRegisteredCount = reports.filter(r => r.etat === 'enregistré').length;
        this.reportsInProgressCount = reports.filter(r => r.etat === 'en cours').length;

        this.totalVotants = surveys.reduce((sum, s) => sum + (s.nbVotants ?? 0), 0);

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        this.upcomingEventsCount = events.filter(e => {
          const d = e['date Evenement'] ? new Date(e['date Evenement'] + 'T00:00:00') : null;
          return d ? d.getTime() >= today.getTime() : false;
        }).length;
      },
      error: () => {
        this.notifications = [];
        this.notifsLoading = false;
        this.notifsLoaded = true;
      }
    });
  }

  private computeNotifications(surveys: Survey[], reports: Report[], events: EventItem[]): Notification[] {
    const notifs: Notification[] = [];
    const userId = Number(localStorage.getItem('userId') || 0);
    const role = localStorage.getItem('userRole') || 'citoyen';
    const dismissed = this.getDismissed();
    const now = new Date();
    const inOneWeek = new Date();
    inOneWeek.setDate(now.getDate() + 7);

    // Le superadmin n'a aucune notification (rôle de configuration uniquement)
    if (role === 'superadmin') {
      return [];
    }

    // Sondages à voter (citoyens uniquement)
    if (role === 'citoyen') {
      surveys.forEach(s => {
        if (s.hasVoted) return;
        if (s.dateFin && new Date(s.dateFin) < now) return;
        const id = `survey-${s.id}`;
        if (dismissed.includes(id)) return;
        notifs.push({
          id,
          type: 'survey',
          title: 'Nouveau sondage à voter',
          message: s.titre,
          route: '/surveys',
          date: s.dateDebut ? new Date(s.dateDebut) : now
        });
      });
    }

    // Signalements selon rôle
    if (role === 'citoyen' && userId) {
      reports.forEach(r => {
        if (r.citoyenId !== userId) return;
        if (r.etat === 'enregistré') return;
        const id = `report-${r.id}-${r.etat}`;
        if (dismissed.includes(id)) return;
        notifs.push({
          id,
          type: 'report',
          title: r.etat === 'résolu' ? 'Signalement résolu' : 'Signalement en cours',
          message: r.typeNom || r.titre || (r.description || '').slice(0, 60),
          route: '/reports',
          date: new Date(r.dateModif || r.dateCrea)
        });
      });
    }

    if (role === 'admin') {
      reports.forEach(r => {
        if (r.etat !== 'enregistré') return;
        const id = `report-admin-${r.id}`;
        if (dismissed.includes(id)) return;
        notifs.push({
          id,
          type: 'report',
          title: 'Nouveau signalement',
          message: r.typeNom || r.titre || (r.description || '').slice(0, 60),
          route: '/backoffice',
          date: new Date(r.dateCrea)
        });
      });
    }

    // Événements à venir cette semaine
    events.forEach((e, idx) => {
      const dateStr = e['date Evenement'];
      if (!dateStr) return;
      const d = new Date(dateStr);
      if (d < now || d > inOneWeek) return;
      const id = `event-${idx}-${dateStr}`;
      if (dismissed.includes(id)) return;
      notifs.push({
        id,
        type: 'event',
        title: 'Événement à venir',
        message: e.titre,
        route: '/events',
        date: d
      });
    });

    notifs.sort((a, b) => b.date.getTime() - a.date.getTime());
    return notifs;
  }

  private getDismissed(): string[] {
    try {
      return JSON.parse(localStorage.getItem(this.dismissedKey) || '[]');
    } catch {
      return [];
    }
  }

  toggleNotifs(event: Event): void {
    event.stopPropagation();
    this.showNotifs = !this.showNotifs;
    if (this.showNotifs && !this.notifsLoaded) {
      this.loadNotifications();
    }
  }

  dismissAll(event: Event): void {
    event.stopPropagation();
    const ids = this.notifications.map(n => n.id);
    const merged = Array.from(new Set([...this.getDismissed(), ...ids]));
    localStorage.setItem(this.dismissedKey, JSON.stringify(merged));
    this.notifications = [];
    this.showNotifs = false;
  }

  goTo(notif: Notification): void {
    // Marque cette notif comme lue (persistance localStorage + retire de la liste affichée)
    const merged = Array.from(new Set([...this.getDismissed(), notif.id]));
    localStorage.setItem(this.dismissedKey, JSON.stringify(merged));
    this.notifications = this.notifications.filter(n => n.id !== notif.id);

    this.showNotifs = false;
    this.router.navigate([notif.route]);
  }

  formatDate(date: Date): string {
    return new Date(date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
  }

  @HostListener('document:click', ['$event.target'])
  onDocClick(target: HTMLElement): void {
    if (!this.showNotifs) return;
    const wrapper = this.el.nativeElement.querySelector('.bell-wrapper');
    if (wrapper && !wrapper.contains(target)) {
      this.showNotifs = false;
    }
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

    if (this.user.role === 'superadmin') {
      // Le superadmin n'a pas besoin des services citoyens (signalements, sondages, etc.)
      return [
        {
          id: 'backoffice',
          title: 'Back-Office',
          description: 'Gérez les signalements et les services',
          icon: 'layout-dashboard',
          color: 'red',
          stats: 'Tableau de bord'
        },
        {
          id: 'settings',
          title: 'Configuration',
          description: 'Paramètres de la mairie et thèmes',
          icon: 'settings',
          color: 'blue',
          stats: 'Personnaliser'
        },
        {
          id: 'comptes',
          title: 'Gestion des comptes',
          description: 'Créer et gérer les citoyens et élus',
          icon: 'users',
          color: 'purple',
          stats: 'Administrer'
        }
      ];
    }

    // Admin : mêmes raccourcis que citoyen, mais redirigés vers les onglets du back-office
    // (logique gérée dans onNavigate). Pas de tuile "Back-Office" dédiée — redondant
    // puisque chaque tuile mène déjà au back-office.
    return citizenModules;
  }

  get welcomeMessage(): string {
    if (this.user.role === 'superadmin') {
      return 'Espace d\'administration globale';
    }
    return this.user.role === 'admin'
      ? 'Tableau de bord pour la gestion des services municipaux'
      : 'Participez à la vie de votre ville';
  }

  onNavigate(pageId: string): void {
    // Pour les admin/superadmin, les raccourcis pointent vers les onglets du back-office
    const isAdmin = this.user.role === 'admin' || this.user.role === 'superadmin';
    if (isAdmin) {
      const tabMap: Record<string, string> = {
        reports: 'reports',
        surveys: 'surveys',
        events: 'events',
        discussion: 'social', // discussion citoyenne (RS) = onglet "Réseaux sociaux" du back-office
        backoffice: ''         // vue d'ensemble
      };
      if (pageId in tabMap) {
        const tab = tabMap[pageId];
        this.router.navigate(tab ? ['/backoffice', tab] : ['/backoffice']);
        return;
      }
    }

    this.router.navigate(['/' + pageId]);
  }

  onSettings(): void {
    this.router.navigate(['/settings']);
  }
}
