import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.scss']
})
export class ForgotPasswordComponent {
  email = '';
  loading = false;
  message = '';
  error = '';

  constructor(private auth: AuthService, private router: Router) {}

  onSubmit(): void {
    this.error = '';
    this.message = '';
    if (!this.email.trim()) {
      this.error = 'Veuillez saisir votre email';
      return;
    }
    this.loading = true;
    this.auth.forgotPassword(this.email.trim()).subscribe({
      next: (res) => {
        this.loading = false;
        this.message = res.message || 'Si un compte existe avec cet email, un lien a été envoyé.';
      },
      error: () => {
        this.loading = false;
        // Même en cas d'erreur réseau, on garde le message générique
        this.message = 'Si un compte existe avec cet email, un lien a été envoyé.';
      }
    });
  }
}
