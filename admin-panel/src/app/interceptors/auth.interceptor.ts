import { HttpInterceptorFn, HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const router = inject(Router);
  const token = authService.getToken();

  // Clone request and add authorization header if token exists
  if (token && !req.url.includes('/auth/login')) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`,
        'Content-Type': req.headers.get('Content-Type') || 'application/json',
        'Accept': 'application/json'
      }
    });
  }

  // Handle the request and catch errors
  return next(req).pipe(
    catchError((error: HttpErrorResponse) => {
      // Handle 401 Unauthorized errors
      if (error.status === 401 && !req.url.includes('/auth/login')) {
        console.warn('Unauthorized request detected, logging out...');
        authService.logout();
        router.navigate(['/login']);
      }

      // Handle 403 Forbidden errors
      if (error.status === 403) {
        console.warn('Access forbidden:', error.url);
      }

      // Handle network errors
      if (error.status === 0) {
        console.error('Network error - unable to reach server');
      }

      return throwError(() => error);
    })
  );
};
