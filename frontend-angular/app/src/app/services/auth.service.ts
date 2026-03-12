import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private apiUrl = 'http://localhost:8000/api/utilisateur';

  constructor(private http: HttpClient) {}

  register(data: {
    nom: string;
    prenom: string;
    email: string;
    motDePasse: string;
    role: number;
    dateNaissance?: string;
    telephone?: string;
  }): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  login(email: string, motDePasse: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { email, motDePasse });
  }

  saveSession(token: string, infoUser: any): void {
    localStorage.setItem('token', token);
    localStorage.setItem('userId', infoUser.id);
    localStorage.setItem('userNom', infoUser.nom);
    localStorage.setItem('userPrenom', infoUser.prenom);
    localStorage.setItem('userEmail', infoUser.email);
    localStorage.setItem('userRole', infoUser.role || 'citoyen');
  }

  logout(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('userId');
    localStorage.removeItem('userNom');
    localStorage.removeItem('userPrenom');
    localStorage.removeItem('userEmail');
    localStorage.removeItem('userRole');
    localStorage.removeItem('villeId');
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  getUserId(): string | null {
    return localStorage.getItem('userId');
  }

  getUserRole(): string {
    return localStorage.getItem('userRole') || 'citoyen';
  }
}
