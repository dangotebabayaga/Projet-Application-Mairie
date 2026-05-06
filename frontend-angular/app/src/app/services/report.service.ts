import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Report {
    id?: number;
    titre: string;
    etat: string;
    description: string;
    adresse?: string;
    typeId?: number;
    utilisateurId?: number; // optionnel car récupéré depuis le token
    dateCrea?: string;
    dateModif?: string;
}
export interface ReportPayload {
  titre: string;
  description: string;
  adresse: string;
  etat?: string;
}

@Injectable({
  providedIn: 'root'
})
export class ReportService {
  private apiUrl = 'http://localhost:8000/api/signalements';

  constructor(private http: HttpClient) {}

  getAll(): Observable<Report[]> {
    return this.http.get<Report[]>(this.apiUrl);
  }

  create(payload: ReportPayload): Observable<any> {
    return this.http.post(this.apiUrl, payload);
  }
}
