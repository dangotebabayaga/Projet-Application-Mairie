import { Component } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';
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

  get navLinks() {
    const links = [
      { label: 'Accueil', route: '/home' },
      { label: 'Signalements', route: '/reports' },
      { label: 'Sondages', route: '/surveys' },
      { label: 'Agenda', route: '/events' },
      { label: 'Discussion', route: '/discussion' }
    ];
    if (this.isAdmin) {
      links.push({ label: 'Back-Office', route: '/backoffice' });
    }
    return links;
  }

  constructor(private router: Router) {
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
        }
      });
  }

  isActive(route: string): boolean {
    return this.router.url === route;
  }

  onLogout(): void {
    localStorage.clear();
    this.router.navigate(['/login']);
  }
}
