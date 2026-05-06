import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

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
  role: 'citoyen' | 'elu';
  address: string;
  neighborhood: string;
}

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent {
  @Output() backToLogin = new EventEmitter<void>();
  @Output() registerSuccess = new EventEmitter<void>();

  formData: RegisterForm = {
    nom: '',
    prenom: '',
    email: '',
    password: '',
    confirmPassword: '',
    role: 'citoyen',
    address: '',
    neighborhood: ''
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

  neighborhoods: string[] = [
    'Centre-ville',
    'Quartier Nord',
    'Quartier Sud',
    'Quartier Est',
    'Quartier Ouest'
  ];

  constructor(private authService: AuthService, private router: Router) {}

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

    this.loading = true;

    const payload = {
      nom: this.formData.nom,
      prenom: this.formData.prenom,
      email: this.formData.email,
      motDePasse: this.formData.password,
      role: [this.formData.role]
    };

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

  selectRole(role: 'citoyen' | 'elu'): void {
    this.formData.role = role;
    if (role === 'elu') {
      this.formData.address = '';
      this.formData.neighborhood = '';
    }
  }

  onBackToLogin(): void {
    this.router.navigate(['/login']);
  }
}
