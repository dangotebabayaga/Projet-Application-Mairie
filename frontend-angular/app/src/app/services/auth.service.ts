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
  }): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  login(email: string, motDePasse: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { email, motDePasse });
  }
}
