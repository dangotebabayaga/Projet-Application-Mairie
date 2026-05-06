import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface AccountUser {
  id: number;
  nom: string;
  prenom: string;
  email: string;
  telephone: string | null;
  role: 'citoyen' | 'admin' | 'superadmin' | 'unknown';
  compteActif: boolean;
  dateCreation: string;
}

export interface CreateAccountPayload {
  nom: string;
  prenom: string;
  email: string;
  motDePasse: string;
  role: 1 | 2; // 1 = citoyen, 2 = admin
  telephone?: string;
}

export interface UpdateAccountPayload {
  nom?: string;
  prenom?: string;
  email?: string;
  telephone?: string;
  motDePasse?: string; // optionnel : si fourni, réinitialise
}

@Injectable({
  providedIn: 'root'
})
export class AccountService {
  private apiUrl = 'http://localhost:8000/api/utilisateurs';

  constructor(private http: HttpClient) {}

  getAll(): Observable<AccountUser[]> {
    return this.http.get<AccountUser[]>(this.apiUrl);
  }

  create(payload: CreateAccountPayload): Observable<any> {
    return this.http.post(this.apiUrl, payload);
  }

  update(id: number, payload: UpdateAccountPayload): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, payload);
  }

  changeRole(id: number, role: 'citoyen' | 'admin'): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}/role`, { role });
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
