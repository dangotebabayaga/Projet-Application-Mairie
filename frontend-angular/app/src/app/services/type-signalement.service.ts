import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface TypeSignalement {
  id: number;
  nom: string;
}

@Injectable({
  providedIn: 'root'
})
export class TypeSignalementService {
  private apiUrl = 'http://localhost:8000/api/types-signalement';

  constructor(private http: HttpClient) {}

  getAll(): Observable<TypeSignalement[]> {
    return this.http.get<TypeSignalement[]>(this.apiUrl);
  }

  create(nom: string): Observable<any> {
    return this.http.post(this.apiUrl, { nom });
  }

  delete(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
