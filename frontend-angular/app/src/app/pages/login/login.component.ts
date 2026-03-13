import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { AppComponent } from '../../app.component';

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
export class LoginComponent implements OnInit {
  email: string = '';
  password: string = '';
  loading: boolean = false;
  error: string = '';
  showPassword: boolean = false;

  cityConfig: CityConfig = {
    name: 'Ma Ville',
    logo: '',
    slogan: 'Une ville connectée'
  };

  constructor(private router: Router, private authService: AuthService, private http: HttpClient) {}

  ngOnInit(): void {
    this.http.get<any>('http://localhost:8000/api/paramettre/1/info').subscribe({
      next: (data) => {
        this.cityConfig.name = data.nom || 'Ma Ville';
        this.cityConfig.slogan = data.slogan || 'Une ville connectée';
        this.cityConfig.logo = data.logo || '';
        if (data.theme) {
          AppComponent.applyTheme(data.theme);
        }
      },
      error: () => {}
    });
  }

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

    this.authService.login(this.email, this.password).subscribe({
      next: (res) => {
        this.loading = false;
        this.authService.saveSession(res.token, res.infoUser);
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
