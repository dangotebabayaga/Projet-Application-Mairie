import { Component } from '@angular/core';
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
  userRole = 'Citoyen';
  isAdmin = false;
  villeLogo = '';
  villeNom = 'Ville Connectée';

  static readonly THEMES: Record<string, { primary: string; primaryDark: string; primaryLight: string }> = {
    blue:   { primary: '#2563EB', primaryDark: '#1D4ED8', primaryLight: '#EFF6FF' },
    green:  { primary: '#16A34A', primaryDark: '#15803D', primaryLight: '#F0FDF4' },
    red:    { primary: '#DC2626', primaryDark: '#B91C1C', primaryLight: '#FEF2F2' },
    purple: { primary: '#7C3AED', primaryDark: '#6D28D9', primaryLight: '#F5F3FF' },
    orange: { primary: '#EA580C', primaryDark: '#C2410C', primaryLight: '#FFF7ED' },
    teal:   { primary: '#0D9488', primaryDark: '#0F766E', primaryLight: '#F0FDFA' }
  };

  static applyTheme(themeId: string): void {
    const theme = AppComponent.THEMES[themeId] || AppComponent.THEMES['blue'];
    const root = document.documentElement;
    root.style.setProperty('--color-primary', theme.primary);
    root.style.setProperty('--color-primary-dark', theme.primaryDark);
    root.style.setProperty('--color-primary-light', theme.primaryLight);
  }

  get navLinks() {
    const links = [
      { label: 'Accueil', route: '/home', icon: 'home' },
      { label: 'Signalements', route: '/reports', icon: 'alert' },
      { label: 'Sondages', route: '/surveys', icon: 'chart' },
      { label: 'Agenda', route: '/events', icon: 'calendar' },
      { label: 'Discussion', route: '/discussion', icon: 'message' }
    ];
    if (this.isAdmin) {
      links.push({ label: 'Back-Office', route: '/backoffice', icon: 'grid' });
      links.push({ label: 'Configuration', route: '/settings', icon: 'settings' });
    }
    return links;
  }

  constructor(private router: Router, private http: HttpClient) {
    this.router.events
      .pipe(filter(event => event instanceof NavigationEnd))
      .subscribe((event: any) => {
        const url = event.urlAfterRedirects || event.url;
        this.showNavbar = !['/login', '/register'].includes(url);
        if (this.showNavbar) {
          this.userName = localStorage.getItem('userPrenom') || '';
          const role = localStorage.getItem('userRole') || 'citoyen';
          this.isAdmin = role === 'admin';
          this.userRole = this.isAdmin ? 'Élu / Admin' : 'Citoyen';
          this.loadVilleConfig();
        }
      });
  }

  isActive(route: string): boolean {
    return this.router.url === route;
  }

  loadVilleConfig(): void {
    const villeId = localStorage.getItem('villeId');
    if (!villeId) return;

    this.http.get<any>(`http://localhost:8000/api/paramettre/${villeId}/info`).subscribe({
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
