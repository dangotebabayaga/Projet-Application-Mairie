import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface EventItem {
  id?: number;
  titre: string;
  lieux: string;
  commentaire: string;
  'date Evenement': string;
  'Heure début': string;
  'Heure fin': string;
  adminId: number;
  type: string;
  photo?: string | null;
}

export interface EventType {
  type: string;
}

export interface EventPayload {
  titre: string;
  lieux: string;
  commentaire: string;
  'date Evenement': string;
  'Heure début': string;
  'Heure fin': string;
  adminId: number;
  type: string;
  photo?: File | null;
}

@Injectable({
  providedIn: 'root'
})
export class EventService {
  private apiUrl = 'http://localhost:8000/api/evenement';

  constructor(private http: HttpClient) {}

  getAll(): Observable<EventItem[]> {
    return this.http.get<EventItem[]>(this.apiUrl);
  }

  getTypes(): Observable<EventType[]> {
    return this.http.get<EventType[]>(`${this.apiUrl}/listeType`);
  }

  create(payload: EventPayload): Observable<any> {
    const fd = new FormData();
    fd.append('titre', payload.titre);
    fd.append('lieux', payload.lieux);
    fd.append('commentaire', payload.commentaire);
    fd.append('date Evenement', payload['date Evenement']);
    fd.append('Heure début', payload['Heure début']);
    fd.append('Heure fin', payload['Heure fin']);
    fd.append('adminId', String(payload.adminId));
    fd.append('type', payload.type);
    if (payload.photo) {
      fd.append('photo', payload.photo);
    }
    return this.http.post(this.apiUrl, fd);
  }

  update(id: number, payload: EventPayload): Observable<any> {
    // POST + override Method via X-HTTP-Method-Override = PUT (utile car certains
    // serveurs PHP gèrent mal les uploads en PUT). Le backend accepte PUT et POST.
    const fd = new FormData();
    fd.append('titre', payload.titre);
    fd.append('lieux', payload.lieux);
    fd.append('commentaire', payload.commentaire);
    fd.append('date Evenement', payload['date Evenement']);
    fd.append('Heure début', payload['Heure début']);
    fd.append('Heure fin', payload['Heure fin']);
    fd.append('type', payload.type);
    if (payload.photo) {
      fd.append('photo', payload.photo);
    }
    return this.http.post(`${this.apiUrl}/${id}`, fd);
  }
}
