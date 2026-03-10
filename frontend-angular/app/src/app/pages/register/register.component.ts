import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';

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
    role: 'citizen',
    address: '',
    neighborhood: ''
  };

  loading: boolean = false;
  error: string = '';
  successMessage: string = '';
  showPassword: boolean = false;
  showConfirmPassword: boolean = false;

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

  constructor(private http: HttpClient, private router: Router) {}

  async onSubmit(): Promise<void> {
    this.error = '';
    this.successMessage = '';

    // Validation
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

    const body = {
      nom: this.formData.nom,
      prenom: this.formData.prenom,
      email: this.formData.email,
      motDePasse: this.formData.password,
      role: this.formData.role === 'citizen' ? 1 : 2
    };

    this.http.post<any>('http://localhost:8000/api/utilisateur/register', body).subscribe({
      next: (res) => {
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

  selectRole(role: 'citizen' | 'municipal'): void {
    this.formData.role = role;
    if (role === 'municipal') {
      this.formData.address = '';
      this.formData.neighborhood = '';
    }
  }

  onBackToLogin(): void {
    this.backToLogin.emit();
  }
}
