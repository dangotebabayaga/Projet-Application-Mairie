import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface ReseauSocial {
  id: number;
  villeId: number;
  plateform: string;
  lien: string;
}

export interface ReseauSocialPayload {
  plateform: string;
  lien: string;
  villeId?: number;
}

@Injectable({
  providedIn: 'root'
})
export class SocialService {
  private apiUrl = 'http://localhost:8000/api/reseau';

  constructor(private http: HttpClient) {}

  getAll(): Observable<ReseauSocial[]> {
    return this.http.get<ReseauSocial[]>(this.apiUrl);
  }

  create(payload: ReseauSocialPayload): Observable<any> {
    return this.http.post(this.apiUrl, payload);
  }

  update(id: number, payload: Partial<ReseauSocialPayload>): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, payload);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
