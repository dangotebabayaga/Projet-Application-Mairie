import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

interface NavItem {
  id: string;
  label: string;
  icon: string;
  roles: string[];
}

interface User {
  name: string;
  role: 'citizen' | 'municipal';
}

interface CityConfig {
  name: string;
  logo: string;
  slogan: string;
}

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss']
})
export class LayoutComponent {
  @Input() currentPage: string = 'home';
  @Output() navigate = new EventEmitter<string>();

  mobileMenuOpen = false;

  // TODO: À remplacer par AuthService
  user: User = {
    name: 'Jean Dupont',
    role: 'citizen'
  };

  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  navItems: NavItem[] = [
    { id: 'home', label: 'Accueil', icon: 'home', roles: ['citizen', 'municipal'] },
    { id: 'reports', label: 'Signalements', icon: 'file-text', roles: ['citizen', 'municipal'] },
    { id: 'surveys', label: 'Sondages', icon: 'bar-chart', roles: ['citizen', 'municipal'] },
    { id: 'events', label: 'Agenda', icon: 'calendar', roles: ['citizen', 'municipal'] },
    { id: 'discussion', label: 'Discussion', icon: 'message-square', roles: ['citizen', 'municipal'] },
    { id: 'backoffice', label: 'Back-Office', icon: 'layout-dashboard', roles: ['municipal'] }
  ];

  get filteredNavItems(): NavItem[] {
    return this.navItems.filter(item =>
      this.user && item.roles.includes(this.user.role)
    );
  }

  get userRoleLabel(): string {
    return this.user?.role === 'municipal' ? 'Élu/Agent' : 'Citoyen';
  }

  onNavigate(pageId: string): void {
    this.navigate.emit(pageId);
    this.mobileMenuOpen = false;
  }

  toggleMobileMenu(): void {
    this.mobileMenuOpen = !this.mobileMenuOpen;
  }

  logout(): void {
    // TODO: Implémenter avec AuthService
    console.log('Logout');
  }
}
