import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { QuartierService, Quartier } from '../../services/quartier.service';
import { CategorieService, Categorie } from '../../services/categorie.service';

interface CityConfig {
  name: string;
  logo: string;
  slogan: string;
}

interface RegisterForm {
  nom: string;
  prenom: string;
  email: string;
  password: string;
  confirmPassword: string;
  address: string;
  quartierId: number | null;
  categorieId: number | null;
}

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {
  @Output() backToLogin = new EventEmitter<void>();
  @Output() registerSuccess = new EventEmitter<void>();

  formData: RegisterForm = {
    nom: '',
    prenom: '',
    email: '',
    password: '',
    confirmPassword: '',
    address: '',
    quartierId: null,
    categorieId: null
  };

  loading: boolean = false;
  error: string = '';
  successMessage: string = '';
  showPassword: boolean = false;
  showConfirmPassword: boolean = false;

  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  quartiers: Quartier[] = [];
  categories: Categorie[] = [];

  constructor(
    private authService: AuthService,
    private router: Router,
    private quartierService: QuartierService,
    private categorieService: CategorieService
  ) {}

  ngOnInit(): void {
    this.quartierService.getAll().subscribe({
      next: (data) => this.quartiers = data,
      error: () => this.quartiers = []
    });
    this.categorieService.getAll().subscribe({
      next: (data) => this.categories = data,
      error: () => this.categories = []
    });
  }

  onSubmit(): void {
    this.error = '';
    this.successMessage = '';

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(this.formData.email)) {
      this.error = "L'adresse email n'est pas valide";
      return;
    }

    if (this.formData.password !== this.formData.confirmPassword) {
      this.error = 'Les mots de passe ne correspondent pas';
      return;
    }

    if (this.formData.password.length < 6) {
      this.error = 'Le mot de passe doit contenir au moins 6 caractères';
      return;
    }

    if (!this.formData.quartierId || !this.formData.categorieId) {
      this.error = 'Veuillez sélectionner votre quartier et votre catégorie';
      return;
    }

    this.loading = true;

    const payload: any = {
      nom: this.formData.nom,
      prenom: this.formData.prenom,
      email: this.formData.email,
      motDePasse: this.formData.password,
      role: 1
    };
    if (this.formData.quartierId) payload.quartierId = this.formData.quartierId;
    if (this.formData.categorieId) payload.categorieId = this.formData.categorieId;

    this.authService.register(payload).subscribe({
      next: () => {
        this.loading = false;
        this.successMessage = 'Votre compte a été créé avec succès ! Vous allez être redirigé vers la page de connexion.';
        this.registerSuccess.emit();
        setTimeout(() => {
          this.router.navigate(['/login']);
        }, 3000);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.error || "Une erreur est survenue lors de l'inscription";
      }
    });
  }

  onBackToLogin(): void {
    this.router.navigate(['/login']);
  }
}
