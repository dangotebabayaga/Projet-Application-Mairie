import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

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
  error: string = '';

  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '🏛️',
    slogan: 'Une ville connectée'
  };

  constructor(private router: Router, private authService: AuthService) {}

  onSubmit(): void {
    if (!this.email || !this.password) return;

    this.error = '';
    this.loading = true;

    this.authService.login(this.email, this.password).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/home']);
      },
      error: () => {
        this.loading = false;
        this.error = 'Email ou mot de passe incorrect';
      }
    });
  }

  async quickLogin(userType: 'citizen' | 'municipal'): Promise<void> {
    this.loading = true;
    try {
      await new Promise(resolve => setTimeout(resolve, 500));
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
