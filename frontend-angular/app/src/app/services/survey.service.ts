import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Choix {
  id: number;
  nom: string;
}

export interface Survey {
  id: number;
  titre: string;
  description: string;
  dateDebut: string;
  dateFin: string;
  idAdmin: number;
  choix: Choix[];
  hasVoted: boolean;
  nbVotants?: number;
  multiChoice?: boolean;
}

export interface VotePayload {
  citoyenId: number;
  sondageId: number;
  choixIds: number[];
}

@Injectable({
  providedIn: 'root'
})
export class SurveyService {
  private apiUrl = 'https://novaville.fr/api/sondages';

  constructor(private http: HttpClient) {}

  getAll(): Observable<Survey[]> {
    return this.http.get<Survey[]>(this.apiUrl);
  }

  vote(payload: VotePayload): Observable<any> {
    return this.http.post(`${this.apiUrl}/vote`, payload);
  }

  getResultat(sondageId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${sondageId}/resultat`);
  }
}
