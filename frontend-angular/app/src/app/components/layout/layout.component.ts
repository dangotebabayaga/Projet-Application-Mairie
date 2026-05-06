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
  role: string[];
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
    role: ['citoyen']
  };

  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  navItems: NavItem[] = [
    { id: 'home',       label: 'Accueil',      icon: 'home',             roles: ['citoyen', 'elu', 'administrateur'] },
    { id: 'reports',    label: 'Signalements', icon: 'file-text',        roles: ['citoyen', 'administrateur'] },
    { id: 'surveys',    label: 'Sondages',     icon: 'bar-chart',        roles: ['citoyen', 'elu', 'administrateur'] },
    { id: 'events',     label: 'Agenda',       icon: 'calendar',         roles: ['citoyen', 'elu', 'administrateur'] },
    { id: 'discussion', label: 'Discussion',   icon: 'message-square',   roles: ['citoyen', 'elu', 'administrateur'] },
    { id: 'backoffice', label: 'Back-Office',  icon: 'layout-dashboard', roles: ['elu', 'administrateur'] }
  ];

  get filteredNavItems(): NavItem[] {
    return this.navItems.filter(item =>
      this.user && item.roles.some(r => this.user.role.includes(r))
    );
  }

  get userRoleLabel(): string {
    return this.user?.role.includes('elu') ? 'Élu/Agent' : 
       this.user?.role.includes('administrateur') ? 'administrateur' : 'Citoyen';
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
