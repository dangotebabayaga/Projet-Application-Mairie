import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const userId = localStorage.getItem('userId');
    if (userId) {
      const cloned = req.clone({
        setHeaders: {
          'X-User-Id': userId
        }
      });
      return next.handle(cloned);
    }
    return next.handle(req);
  }
}
