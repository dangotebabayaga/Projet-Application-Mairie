import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { AppComponent } from '../../app.component';

interface ThemeOption {
  id: string;
  name: string;
  primary: string;
  primaryDark: string;
  primaryLight: string;
}

interface VilleConfig {
  id: number;
  nom: string;
  slogan: string;
  logo: string;
  theme: string;
}

@Component({
  selector: 'app-settings',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.scss']
})
export class SettingsComponent implements OnInit {
  villeConfig: VilleConfig = {
    id: 0,
    nom: '',
    slogan: '',
    logo: '',
    theme: 'blue'
  };

  selectedTheme = 'blue';
  loading = true;
  saving = false;
  error = '';
  success = '';

  themes: ThemeOption[] = [
    { id: 'blue', name: 'Bleu', primary: '#2563EB', primaryDark: '#1D4ED8', primaryLight: '#EFF6FF' },
    { id: 'green', name: 'Vert', primary: '#16A34A', primaryDark: '#15803D', primaryLight: '#F0FDF4' },
    { id: 'red', name: 'Rouge', primary: '#DC2626', primaryDark: '#B91C1C', primaryLight: '#FEF2F2' },
    { id: 'purple', name: 'Violet', primary: '#7C3AED', primaryDark: '#6D28D9', primaryLight: '#F5F3FF' },
    { id: 'orange', name: 'Orange', primary: '#EA580C', primaryDark: '#C2410C', primaryLight: '#FFF7ED' },
    { id: 'teal', name: 'Turquoise', primary: '#0D9488', primaryDark: '#0F766E', primaryLight: '#F0FDFA' }
  ];

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    const role = localStorage.getItem('userRole');
    if (role !== 'admin') {
      this.router.navigate(['/home']);
      return;
    }
    this.loadConfig();
  }

  loadConfig(): void {
    const villeId = localStorage.getItem('villeId');
    if (!villeId) {
      this.loading = false;
      this.error = 'Aucune ville associée à votre compte';
      return;
    }

    this.http.get<any>(`http://localhost:8000/api/paramettre/${villeId}/info`).subscribe({
      next: (data) => {
        this.villeConfig = {
          id: data.id,
          nom: data.nom || '',
          slogan: data.slogan || '',
          logo: data.logo || '',
          theme: data.theme || 'blue'
        };
        this.selectedTheme = this.villeConfig.theme || 'blue';
        this.loading = false;
      },
      error: () => {
        this.loading = false;
        this.error = 'Impossible de charger la configuration';
      }
    });
  }

  selectTheme(themeId: string): void {
    this.selectedTheme = themeId;
    this.villeConfig.theme = themeId;
  }

  getTheme(id: string): ThemeOption {
    return this.themes.find(t => t.id === id) || this.themes[0];
  }

  onSave(): void {
    if (!this.villeConfig.nom) {
      this.error = 'Le nom de la ville est obligatoire';
      return;
    }

    this.saving = true;
    this.error = '';
    this.success = '';

    const userId = localStorage.getItem('userId');

    const payload = {
      id: this.villeConfig.id,
      nom: this.villeConfig.nom,
      slogan: this.villeConfig.slogan,
      logo: this.villeConfig.logo,
      theme: this.selectedTheme,
      administrateur_Id: parseInt(userId || '0')
    };

    this.http.post('http://localhost:8000/api/paramettre', payload).subscribe({
      next: () => {
        this.saving = false;
        this.success = 'Configuration sauvegardée avec succès !';
        AppComponent.applyTheme(this.selectedTheme);
        setTimeout(() => this.success = '', 3000);
      },
      error: (err) => {
        this.saving = false;
        this.error = err.error?.error || 'Erreur lors de la sauvegarde';
      }
    });
  }
}
