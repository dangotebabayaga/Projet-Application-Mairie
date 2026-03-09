import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
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
  role: 'citizen' | 'municipal';
  address: string;
  neighborhood: string;
}

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule],
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
    role: 'citizen',
    address: '',
    neighborhood: ''
  };

  constructor(private authService: AuthService, private router: Router) {}

  loading: boolean = false;
  error: string = '';

  // TODO: À remplacer par AuthService
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

  onSubmit(): void {
    this.error = '';

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
      role: this.formData.role === 'citizen' ? 1 : 2
    };

    this.authService.register(payload).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/login']);
      },
      error: (err) => {
        this.loading = false;
        this.error = "Une erreur est survenue lors de l'inscription";
        console.error('Register error:', err);
      }
    });
  }

  selectRole(role: 'citizen' | 'municipal'): void {
    this.formData.role = role;
    if (role === 'municipal') {
      this.formData.address = '';
      this.formData.neighborhood = '';
    }
  }

  onBackToLogin(): void {
    this.router.navigate(['/login']);
  }
}
