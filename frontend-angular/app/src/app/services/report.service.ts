import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Report {
  id: number;
  titre: string;
  etat: string;
  description: string;
  adresse: string | null;
  latitude: number | null;
  longitude: number | null;
  typeId: number | null;
  typeNom: string | null;
  photo: string | null;
  citoyenId: number | null;
  dateCrea: string;
  dateModif: string;
}

export interface ReportPayload {
  titre: string;
  description: string;
  adresse: string;
  typeId: number | null;
  photo?: File | null;
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
    const fd = new FormData();
    fd.append('titre', payload.titre);
    fd.append('description', payload.description);
    fd.append('adresse', payload.adresse);
    if (payload.typeId !== null && payload.typeId !== undefined) {
      fd.append('typeId', String(payload.typeId));
    }
    if (payload.photo) {
      fd.append('photo', payload.photo);
    }
    return this.http.post(this.apiUrl, fd);
  }

  advanceState(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`);
  }

  update(id: number, payload: ReportPayload): Observable<any> {
    const fd = new FormData();
    fd.append('titre', payload.titre);
    fd.append('description', payload.description);
    fd.append('adresse', payload.adresse);
    if (payload.typeId !== null && payload.typeId !== undefined) {
      fd.append('typeId', String(payload.typeId));
    }
    if (payload.photo) {
      fd.append('photo', payload.photo);
    }
    // POST sur /:id (le backend accepte aussi PUT, mais POST gère mieux multipart)
    return this.http.post(`${this.apiUrl}/${id}`, fd);
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
