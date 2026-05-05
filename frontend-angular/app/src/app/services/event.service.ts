import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface EventItem {
  titre: string;
  lieux: string;
  commentaire: string;
  'date Evenement': string;
  'Heure début': string;
  'Heure fin': string;
  adminId: number;
  type: string;
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
    return this.http.post(this.apiUrl, payload);
  }
}
