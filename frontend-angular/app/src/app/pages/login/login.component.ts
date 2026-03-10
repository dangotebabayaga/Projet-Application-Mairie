import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';

interface CityConfig {
  name: string;
  logo: string;
  slogan: string;
}

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  loading: boolean = false;
  error: string = '';
  showPassword: boolean = false;

  // TODO: À remplacer par AuthService
  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  constructor(private router: Router, private http: HttpClient) {}

  isValidEmail(email: string): boolean {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
  }

  onSubmit(): void {
    if (!this.email || !this.password) return;

    if (!this.isValidEmail(this.email)) {
      this.error = "L'adresse email n'est pas valide";
      return;
    }

    this.loading = true;
    this.error = '';

    this.http.post<any>('http://localhost:8000/api/utilisateur/login', {
      email: this.email,
      motDePasse: this.password
    }).subscribe({
      next: (res) => {
        this.loading = false;
        localStorage.setItem('userId', res.id);
        localStorage.setItem('userEmail', res.email);
        localStorage.setItem('userPrenom', res.prenom);
        this.router.navigate(['/home']);
      },
      error: (err) => {
        this.loading = false;
        this.error = err.error?.error || 'Email ou mot de passe incorrect';
      }
    });
  }

  onRegister(): void {
    this.router.navigate(['/register']);
  }
}
