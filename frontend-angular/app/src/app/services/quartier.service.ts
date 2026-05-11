import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Quartier {
  id: number;
  nom: string;
  villeId: number;
}

@Injectable({
  providedIn: 'root'
})
export class QuartierService {
  private apiUrl = 'https://novaville.fr/api/quartiers';

  constructor(private http: HttpClient) {}

  getAll(): Observable<Quartier[]> {
    return this.http.get<Quartier[]>(this.apiUrl);
  }

  create(nom: string): Observable<any> {
    return this.http.post(this.apiUrl, { nom });
  }

  update(id: number, nom: string): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, { nom });
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
