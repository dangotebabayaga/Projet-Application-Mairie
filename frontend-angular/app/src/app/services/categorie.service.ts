import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Categorie {
  id: number;
  libelle: string;
  villeId: number;
}

@Injectable({
  providedIn: 'root'
})
export class CategorieService {
  private apiUrl = 'http://localhost:8000/api/categories';

  constructor(private http: HttpClient) {}

  getAll(): Observable<Categorie[]> {
    return this.http.get<Categorie[]>(this.apiUrl);
  }

  create(libelle: string): Observable<any> {
    return this.http.post(this.apiUrl, { libelle });
  }

  update(id: number, libelle: string): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, { libelle });
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
