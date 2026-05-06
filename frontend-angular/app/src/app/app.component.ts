import { Component, HostListener, ElementRef } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { filter } from 'rxjs/operators';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'myapp';
  showNavbar = false;
  userName = '';
  userPrenom = '';
  userNom = '';
  userEmail = '';
  userRole = 'Citoyen';
  isAdmin = false;
  isSuperAdmin = false;
  superAdminExtended = false;
  villeLogo = '';
  villeNom = 'Ville Connectée';

  showUserMenu = false;
  mobileMenuOpen = false;

  static readonly THEMES: Record<string, Record<string, string>> = {
    light: {
      '--color-primary':       '#2563EB',
      '--color-primary-dark':  '#1D4ED8',
      '--color-primary-light': '#EFF6FF',
      '--color-bg':            '#F9FAFB',
      '--color-surface':       '#FFFFFF',
      '--color-surface-alt':   '#F3F4F6',
      '--color-text':          '#111827',
      '--color-text-muted':    '#6B7280',
      '--color-border':        '#E5E7EB'
    },
    dark: {
      '--color-primary':       '#60A5FA',
      '--color-primary-dark':  '#3B82F6',
      '--color-primary-light': 'rgba(96, 165, 250, 0.18)',
      '--color-bg':            '#0F172A',
      '--color-surface':       '#1E293B',
      '--color-surface-alt':   '#334155',
      '--color-text':          '#F8FAFC',
      '--color-text-muted':    '#94A3B8',
      '--color-border':        '#334155'
    },
    dusk: {
      '--color-primary':       '#FB923C',
      '--color-primary-dark':  '#EA580C',
      '--color-primary-light': 'rgba(251, 146, 60, 0.18)',
      '--color-bg':            '#1A1625',
      '--color-surface':       '#2D2640',
      '--color-surface-alt':   '#3D3552',
      '--color-text':          '#F5E9D5',
      '--color-text-muted':    '#B0A1B5',
      '--color-border':        '#3D3552'
    },
    cream: {
      '--color-primary':       '#C2410C',
      '--color-primary-dark':  '#9A3412',
      '--color-primary-light': '#FEF3E2',
      '--color-bg':            '#FAF7F2',
      '--color-surface':       '#FFFFFF',
      '--color-surface-alt':   '#F0EBE2',
      '--color-text':          '#44403C',
      '--color-text-muted':    '#78716C',
      '--color-border':        '#E7E0D3'
    },
    monochrome: {
      '--color-primary':       '#000000',
      '--color-primary-dark':  '#000000',
      '--color-primary-light': '#F4F4F5',
      '--color-bg':            '#FFFFFF',
      '--color-surface':       '#FAFAFA',
      '--color-surface-alt':   '#F4F4F5',
      '--color-text':          '#000000',
      '--color-text-muted':    '#71717A',
      '--color-border':        '#E4E4E7'
    },
    ocean: {
      '--color-primary':       '#22D3EE',
      '--color-primary-dark':  '#06B6D4',
      '--color-primary-light': 'rgba(34, 211, 238, 0.18)',
      '--color-bg':            '#0B2447',
      '--color-surface':       '#19376D',
      '--color-surface-alt':   '#2C5282',
      '--color-text':          '#E0F2FE',
      '--color-text-muted':    '#A5C9E5',
      '--color-border':        '#2C5282'
    },
    forest: {
      '--color-primary':       '#A3E635',
      '--color-primary-dark':  '#65A30D',
      '--color-primary-light': 'rgba(163, 230, 53, 0.18)',
      '--color-bg':            '#0F1F0F',
      '--color-surface':       '#1A331A',
      '--color-surface-alt':   '#264026',
      '--color-text':          '#F0FDF4',
      '--color-text-muted':    '#A3B8A3',
      '--color-border':        '#264026'
    },
    sunset: {
      '--color-primary':       '#F472B6',
      '--color-primary-dark':  '#DB2777',
      '--color-primary-light': 'rgba(244, 114, 182, 0.18)',
      '--color-bg':            '#2E1065',
      '--color-surface':       '#4C1D95',
      '--color-surface-alt':   '#5B21B6',
      '--color-text':          '#FBE9D5',
      '--color-text-muted':    '#C4B5D9',
      '--color-border':        '#5B21B6'
    },
    lavender: {
      '--color-primary':       '#7C3AED',
      '--color-primary-dark':  '#5B21B6',
      '--color-primary-light': '#EDE9FE',
      '--color-bg':            '#F5F3FF',
      '--color-surface':       '#FFFFFF',
      '--color-surface-alt':   '#EDE9FE',
      '--color-text':          '#3B0764',
      '--color-text-muted':    '#7E5BB3',
      '--color-border':        '#DDD6FE'
    },
    cyberpunk: {
      '--color-primary':       '#22D3EE',
      '--color-primary-dark':  '#06B6D4',
      '--color-primary-light': 'rgba(236, 72, 153, 0.18)',
      '--color-bg':            '#000000',
      '--color-surface':       '#0A0A0F',
      '--color-surface-alt':   '#18181B',
      '--color-text':          '#F0ABFC',
      '--color-text-muted':    '#22D3EE',
      '--color-border':        '#EC4899'
    }
  };

  // Compat : anciens IDs (blue, green, red…) → light
  private static readonly LEGACY_ALIASES: Record<string, string> = {
    blue: 'light', green: 'light', red: 'light',
    purple: 'light', orange: 'light', teal: 'light'
  };

  static applyTheme(themeId: string): void {
    const resolved = AppComponent.LEGACY_ALIASES[themeId] || themeId;
    const theme = AppComponent.THEMES[resolved] || AppComponent.THEMES['light'];
    const root = document.documentElement;
    Object.entries(theme).forEach(([key, value]) => {
      root.style.setProperty(key, value);
    });
  }

  get navLinks() {
    // Le superadmin voit par défaut Back-Office + Configuration + Gestion des comptes
    // Les autres pages ne s'affichent que si "Mode étendu" est activé
    if (this.isSuperAdmin) {
      const adminLinks = [
        { label: 'Back-Office', route: '/backoffice', icon: 'grid' },
        { label: 'Configuration', route: '/settings', icon: 'settings' },
        { label: 'Gestion des comptes', route: '/comptes', icon: 'users' }
      ];
      if (this.superAdminExtended) {
        return [
          { label: 'Accueil', route: '/home', icon: 'home' },
          { label: 'Signalements', route: '/reports', icon: 'alert' },
          { label: 'Sondages', route: '/surveys', icon: 'chart' },
          { label: 'Agenda', route: '/events', icon: 'calendar' },
          { label: 'Discussion', route: '/discussion', icon: 'message' },
          ...adminLinks
        ];
      }
      return adminLinks;
    }

    // Élu/Admin : navbar épurée — tout passe par le Back-Office
    if (this.isAdmin) {
      return [
        { label: 'Accueil', route: '/home', icon: 'home' },
        { label: 'Back-Office', route: '/backoffice', icon: 'grid' },
        { label: 'Configuration', route: '/settings', icon: 'settings' }
      ];
    }

    // Citoyen : pages publiques
    return [
      { label: 'Accueil', route: '/home', icon: 'home' },
      { label: 'Signalements', route: '/reports', icon: 'alert' },
      { label: 'Sondages', route: '/surveys', icon: 'chart' },
      { label: 'Agenda', route: '/events', icon: 'calendar' },
      { label: 'Discussion', route: '/discussion', icon: 'message' }
    ];
  }

  constructor(private router: Router, private http: HttpClient, private el: ElementRef) {
    this.router.events
      .pipe(filter(event => event instanceof NavigationEnd))
      .subscribe((event: any) => {
        const url = event.urlAfterRedirects || event.url;
        this.showNavbar = !['/login', '/register'].includes(url);
        this.showUserMenu = false;
        this.mobileMenuOpen = false;
        if (this.showNavbar) {
          this.userPrenom = localStorage.getItem('userPrenom') || '';
          this.userNom = localStorage.getItem('userNom') || '';
          this.userEmail = localStorage.getItem('userEmail') || '';
          this.userName = this.userPrenom;
          const role = localStorage.getItem('userRole') || 'citoyen';
          this.isSuperAdmin = role === 'superadmin';
          this.isAdmin = role === 'admin' || this.isSuperAdmin;
          this.userRole = this.isSuperAdmin ? 'Super-admin' : (role === 'admin' ? 'Élu / Admin' : 'Citoyen');
          this.superAdminExtended = localStorage.getItem('superAdminExtended') === 'true';
          this.loadVilleConfig();
        }
      });
  }

  toggleUserMenu(event: Event): void {
    event.stopPropagation();
    this.showUserMenu = !this.showUserMenu;
  }

  toggleMobileMenu(): void {
    this.mobileMenuOpen = !this.mobileMenuOpen;
  }

  @HostListener('document:click', ['$event.target'])
  onDocClick(target: HTMLElement): void {
    if (!this.showUserMenu) return;
    const wrapper = this.el.nativeElement.querySelector('.user-menu-wrapper');
    if (wrapper && !wrapper.contains(target)) {
      this.showUserMenu = false;
    }
  }

  isActive(route: string): boolean {
    // Match exact OU préfixe suivi d'un '/' (pour /backoffice/reports → match /backoffice)
    // On split aussi sur '?' pour ignorer les query params éventuels
    const url = this.router.url.split('?')[0];
    return url === route || url.startsWith(route + '/');
  }

  loadVilleConfig(): void {
    this.http.get<any>(`http://localhost:8000/api/paramettre/info`).subscribe({
      next: (data) => {
        this.villeNom = data.nom || 'Ville Connectée';
        this.villeLogo = data.logo || '';
        if (data.theme) {
          AppComponent.applyTheme(data.theme);
        }
      },
      error: () => {}
    });
  }

  onLogout(): void {
    localStorage.clear();
    this.router.navigate(['/login']);
  }
}
