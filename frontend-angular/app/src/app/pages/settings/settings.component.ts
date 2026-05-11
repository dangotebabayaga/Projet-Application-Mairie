import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { AppComponent } from '../../app.component';
import { QuartierService, Quartier } from '../../services/quartier.service';
import { CategorieService, Categorie } from '../../services/categorie.service';

interface ThemeOption {
  id: string;
  name: string;
  description: string;
  primary: string;
  bg: string;
  surface: string;
  text: string;
  border: string;
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
    id: 1,
    nom: '',
    slogan: '',
    logo: '',
    theme: 'light'
  };

  selectedTheme = 'light';
  loading = true;
  saving = false;
  error = '';
  success = '';

  isSuperAdmin = false;
  superAdminExtended = false;

  // Quartiers
  quartiers: Quartier[] = [];
  newQuartierNom = '';
  quartierError = '';

  // Catégories
  categories: Categorie[] = [];
  newCategorieLibelle = '';
  categorieError = '';

  themes: ThemeOption[] = [
    {
      id: 'light',
      name: 'Clair',
      description: 'Lumineux et neutre, idéal pour la lecture diurne',
      primary: '#2563EB',
      bg: '#F9FAFB',
      surface: '#FFFFFF',
      text: '#111827',
      border: '#E5E7EB'
    },
    {
      id: 'dark',
      name: 'Sombre',
      description: 'Mode nuit, repose les yeux et économise la batterie',
      primary: '#60A5FA',
      bg: '#0F172A',
      surface: '#1E293B',
      text: '#F8FAFC',
      border: '#334155'
    },
    {
      id: 'dusk',
      name: 'Crépuscule',
      description: 'Ambiance chaleureuse, accents corail sur fond plum',
      primary: '#FB923C',
      bg: '#1A1625',
      surface: '#2D2640',
      text: '#F5E9D5',
      border: '#3D3552'
    },
    {
      id: 'cream',
      name: 'Crème',
      description: 'Doux et accueillant, accents terracotta sur fond crème',
      primary: '#C2410C',
      bg: '#FAF7F2',
      surface: '#FFFFFF',
      text: '#44403C',
      border: '#E7E0D3'
    },
    {
      id: 'monochrome',
      name: 'Mono',
      description: 'Minimal et contrasté, noir sur blanc strict',
      primary: '#000000',
      bg: '#FFFFFF',
      surface: '#FAFAFA',
      text: '#000000',
      border: '#E4E4E7'
    },
    {
      id: 'ocean',
      name: 'Océan',
      description: 'Bleu marine profond, accents turquoise lumineux',
      primary: '#22D3EE',
      bg: '#0B2447',
      surface: '#19376D',
      text: '#E0F2FE',
      border: '#2C5282'
    },
    {
      id: 'forest',
      name: 'Forêt',
      description: 'Sapin sombre, accents lime énergiques',
      primary: '#A3E635',
      bg: '#0F1F0F',
      surface: '#1A331A',
      text: '#F0FDF4',
      border: '#264026'
    },
    {
      id: 'sunset',
      name: 'Coucher de soleil',
      description: 'Violet aubergine, accents rose chaleureux',
      primary: '#F472B6',
      bg: '#2E1065',
      surface: '#4C1D95',
      text: '#FBE9D5',
      border: '#5B21B6'
    },
    {
      id: 'lavender',
      name: 'Lavande',
      description: 'Pastel mauve clair, accents violet doux',
      primary: '#7C3AED',
      bg: '#F5F3FF',
      surface: '#FFFFFF',
      text: '#3B0764',
      border: '#DDD6FE'
    },
    {
      id: 'cyberpunk',
      name: 'Cyberpunk',
      description: 'Néon cyan/magenta sur noir profond',
      primary: '#22D3EE',
      bg: '#000000',
      surface: '#0A0A0F',
      text: '#F0ABFC',
      border: '#EC4899'
    }
  ];

  constructor(
    private http: HttpClient,
    private router: Router,
    private quartierService: QuartierService,
    private categorieService: CategorieService
  ) {}

  ngOnInit(): void {
    const role = localStorage.getItem('userRole');
    if (role !== 'admin' && role !== 'superadmin') {
      this.router.navigate(['/home']);
      return;
    }
    this.isSuperAdmin = role === 'superadmin';
    this.superAdminExtended = localStorage.getItem('superAdminExtended') === 'true';
    this.loadConfig();
    this.loadQuartiers();
    this.loadCategories();
  }

  // ---- Quartiers ----

  loadQuartiers(): void {
    this.quartierService.getAll().subscribe({
      next: (data) => this.quartiers = data,
      error: () => this.quartiers = []
    });
  }

  addQuartier(): void {
    this.quartierError = '';
    const nom = this.newQuartierNom.trim();
    if (!nom) { this.quartierError = 'Nom obligatoire'; return; }
    this.quartierService.create(nom).subscribe({
      next: () => {
        this.newQuartierNom = '';
        this.loadQuartiers();
      },
      error: (err) => this.quartierError = err.error?.error || 'Erreur lors de la création'
    });
  }

  deleteQuartier(q: Quartier): void {
    if (!confirm(`Supprimer le quartier "${q.nom}" ?`)) return;
    this.quartierService.delete(q.id).subscribe({
      next: () => this.loadQuartiers(),
      error: (err) => alert(err.error?.error || 'Erreur lors de la suppression')
    });
  }

  // ---- Catégories ----

  loadCategories(): void {
    this.categorieService.getAll().subscribe({
      next: (data) => this.categories = data,
      error: () => this.categories = []
    });
  }

  addCategorie(): void {
    this.categorieError = '';
    const libelle = this.newCategorieLibelle.trim();
    if (!libelle) { this.categorieError = 'Libellé obligatoire'; return; }
    this.categorieService.create(libelle).subscribe({
      next: () => {
        this.newCategorieLibelle = '';
        this.loadCategories();
      },
      error: (err) => this.categorieError = err.error?.error || 'Erreur lors de la création'
    });
  }

  deleteCategorie(c: Categorie): void {
    if (!confirm(`Supprimer la catégorie "${c.libelle}" ?`)) return;
    this.categorieService.delete(c.id).subscribe({
      next: () => this.loadCategories(),
      error: (err) => alert(err.error?.error || 'Erreur lors de la suppression')
    });
  }

  toggleExtendedMode(): void {
    this.superAdminExtended = !this.superAdminExtended;
    localStorage.setItem('superAdminExtended', String(this.superAdminExtended));
    // Recharger la page pour que la navbar prenne en compte le changement
    window.location.reload();
  }

  loadConfig(): void {
    const villeId = localStorage.getItem('villeId');
    if (!villeId) {
      this.loading = false;
      this.error = 'Aucune ville associée à votre compte';
      return;
    }

    this.http.get<any>(`https://novaville.fr/api/paramettre/info`).subscribe({
      next: (data) => {
        this.villeConfig = {
          id: data.id,
          nom: data.nom || '',
          slogan: data.slogan || '',
          logo: data.logo || '',
          theme: data.theme || 'light'
        };
        this.selectedTheme = this.villeConfig.theme || 'light';
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

    this.http.post('https://novaville.fr/api/paramettre', payload).subscribe({
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
