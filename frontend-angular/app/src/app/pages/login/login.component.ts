import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

interface CityConfig {
  name: string;
  logo: string;
  slogan: string;
}

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  loading: boolean = false;

  // TODO: À remplacer par AuthService
  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  constructor(private router: Router) {}

  async onSubmit(): Promise<void> {
    if (!this.email || !this.password) return;

    this.loading = true;
    try {
      // TODO: Appeler AuthService.login() et vérifier le rôle
      console.log('Login attempt:', this.email);
      await new Promise(resolve => setTimeout(resolve, 1000));
      // Par défaut, rediriger vers home (citoyen)
      this.router.navigate(['/home']);
    } catch (error) {
      console.error('Login error:', error);
    } finally {
      this.loading = false;
    }
  }

  async quickLogin(userType: 'citizen' | 'municipal'): Promise<void> {
    this.loading = true;

    try {
      console.log('Quick login:', userType);
      await new Promise(resolve => setTimeout(resolve, 500));

      // Redirection selon le type d'utilisateur
      if (userType === 'citizen') {
        this.router.navigate(['/home']);
      } else {
        this.router.navigate(['/backoffice']);
      }
    } finally {
      this.loading = false;
    }
  }

  onRegister(): void {
    this.router.navigate(['/register']);
  }
}
