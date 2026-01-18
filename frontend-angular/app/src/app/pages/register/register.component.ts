import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

interface CityConfig {
  name: string;
  logo: string;
  slogan: string;
}

interface RegisterForm {
  name: string;
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
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    role: 'citizen',
    address: '',
    neighborhood: ''
  };

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

  async onSubmit(): Promise<void> {
    this.error = '';

    // Validation
    if (this.formData.password !== this.formData.confirmPassword) {
      this.error = 'Les mots de passe ne correspondent pas';
      return;
    }

    if (this.formData.password.length < 6) {
      this.error = 'Le mot de passe doit contenir au moins 6 caractères';
      return;
    }

    this.loading = true;
    try {
      // TODO: Appeler AuthService.register()
      console.log('Register attempt:', this.formData);
      await new Promise(resolve => setTimeout(resolve, 1000));
      this.registerSuccess.emit();
    } catch (error) {
      this.error = "Une erreur est survenue lors de l'inscription";
      console.error('Register error:', error);
    } finally {
      this.loading = false;
    }
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
