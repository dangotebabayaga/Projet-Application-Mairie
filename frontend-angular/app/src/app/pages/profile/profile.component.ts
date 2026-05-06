import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { QuartierService, Quartier } from '../../services/quartier.service';
import { CategorieService, Categorie } from '../../services/categorie.service';

interface ProfileForm {
  nom: string;
  prenom: string;
  email: string;
  telephone: string;
  dateNaissance: string;
  quartierId: number | null;
  categorieId: number | null;
}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {
  form: ProfileForm = {
    nom: '',
    prenom: '',
    email: '',
    telephone: '',
    dateNaissance: '',
    quartierId: null,
    categorieId: null
  };

  role = '';
  loading = true;
  saving = false;
  error = '';
  successMessage = '';

  quartiers: Quartier[] = [];
  categories: Categorie[] = [];

  constructor(
    private auth: AuthService,
    private router: Router,
    private quartierService: QuartierService,
    private categorieService: CategorieService
  ) {}

  ngOnInit(): void {
    this.loadProfile();
    this.quartierService.getAll().subscribe({
      next: (d) => this.quartiers = d,
      error: () => this.quartiers = []
    });
    this.categorieService.getAll().subscribe({
      next: (d) => this.categories = d,
      error: () => this.categories = []
    });
  }

  get isCitoyen(): boolean {
    return this.role === 'citoyen';
  }

  loadProfile(): void {
    this.loading = true;
    this.error = '';
    this.auth.getMe().subscribe({
      next: (data) => {
        this.form = {
          nom: data.nom || '',
          prenom: data.prenom || '',
          email: data.email || '',
          telephone: data.telephonne || '',
          dateNaissance: this.toDateInput(data['date Naissance']),
          quartierId: data.quartierId ?? null,
          categorieId: data.categorieId ?? null
        };
        this.role = data.role || '';
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Impossible de charger vos informations';
        this.loading = false;
        console.error(err);
      }
    });
  }

  private toDateInput(value: any): string {
    if (!value) return '';
    if (typeof value === 'string') return value.substring(0, 10);
    if (value.date) return String(value.date).substring(0, 10);
    try {
      return new Date(value).toISOString().substring(0, 10);
    } catch {
      return '';
    }
  }

  onSubmit(): void {
    if (this.saving) return;
    this.error = '';
    this.successMessage = '';
    this.saving = true;

    const payload: any = {
      nom: this.form.nom,
      prenom: this.form.prenom,
      email: this.form.email,
      telephone: this.form.telephone || '',
      dateNaissance: this.form.dateNaissance || ''
    };
    if (this.isCitoyen) {
      payload.quartierId = this.form.quartierId ?? null;
      payload.categorieId = this.form.categorieId ?? null;
    }

    this.auth.updateMe(payload).subscribe({
      next: (resp) => {
        this.saving = false;
        this.successMessage = 'Vos informations ont été mises à jour';

        const info = resp.infoUser || {};
        if (info.nom) localStorage.setItem('userNom', info.nom);
        if (info.prenom) localStorage.setItem('userPrenom', info.prenom);
        if (info.email) localStorage.setItem('userEmail', info.email);
      },
      error: (err) => {
        this.saving = false;
        this.error = err.error?.error || 'Erreur lors de la mise à jour';
        console.error(err);
      }
    });
  }

  onCancel(): void {
    this.router.navigate(['/home']);
  }

  get roleLabel(): string {
    if (this.role === 'admin') return 'Élu / Admin';
    if (this.role === 'superadmin') return 'Super-admin';
    return 'Citoyen';
  }
}
