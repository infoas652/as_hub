import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError, timeout, retry, catchError } from 'rxjs';
import { environment } from '../../environments/environment';

export interface ContentResponse {
  success?: boolean;
  language?: string;
  data?: {
    services: any[];
    pricing: any[];
    pricing_by_service?: {
      website: any[];
      app: any[];
      both: any[];
    };
    features: any[];
    testimonials: any[];
    faq: any[];
    settings: any;
  };
  services?: any[];
  pricing?: any[];
  features?: any[];
  testimonials?: any[];
  faq?: any[];
  settings?: any;
}

export interface LeadRequest {
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  plan?: string;
}

export interface ApiError {
  message: string;
  status: number;
  error?: any;
}

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = environment.apiUrl;
  private readonly timeout = environment.apiTimeout || 30000;
  private readonly maxRetries = 2;

  constructor(private http: HttpClient) {}

  /**
   * Get all landing page content with caching
   */
  getContent(language: string = 'en'): Observable<ContentResponse> {
    return this.http.get<ContentResponse>(`${this.apiUrl}/v1/content?language=${language}`)
      .pipe(
        timeout(this.timeout),
        retry(this.maxRetries),
        catchError(this.handleError)
      );
  }

  /**
   * Submit contact form with validation
   */
  submitLead(data: LeadRequest): Observable<any> {
    // Validate required fields
    if (!data.name || !data.email || !data.message) {
      return throwError(() => ({
        message: 'Name, email, and message are required',
        status: 400
      }));
    }

    return this.http.post(`${this.apiUrl}/v1/leads`, data)
      .pipe(
        timeout(this.timeout),
        catchError(this.handleError)
      );
  }

  /**
   * Health check with timeout
   */
  healthCheck(): Observable<any> {
    return this.http.get(`${this.apiUrl}/health`)
      .pipe(
        timeout(5000), // Shorter timeout for health check
        catchError(this.handleError)
      );
  }

  /**
   * Centralized error handler
   */
  private handleError(error: HttpErrorResponse): Observable<never> {
    let errorMessage: ApiError = {
      message: 'An unknown error occurred',
      status: 0
    };

    if (error.error instanceof ErrorEvent) {
      // Client-side error
      errorMessage = {
        message: `Client Error: ${error.error.message}`,
        status: 0,
        error: error.error
      };
    } else {
      // Server-side error
      errorMessage = {
        message: error.error?.message || `Server Error: ${error.status} - ${error.statusText}`,
        status: error.status,
        error: error.error
      };

      // Handle specific status codes
      switch (error.status) {
        case 0:
          errorMessage.message = 'Unable to connect to server. Please check your internet connection.';
          break;
        case 400:
          errorMessage.message = error.error?.message || 'Invalid request. Please check your input.';
          break;
        case 401:
          errorMessage.message = 'Unauthorized. Please login again.';
          break;
        case 403:
          errorMessage.message = 'Access denied.';
          break;
        case 404:
          errorMessage.message = 'Resource not found.';
          break;
        case 422:
          errorMessage.message = error.error?.message || 'Validation error. Please check your input.';
          break;
        case 429:
          errorMessage.message = 'Too many requests. Please try again later.';
          break;
        case 500:
          errorMessage.message = 'Server error. Please try again later.';
          break;
        case 503:
          errorMessage.message = 'Service temporarily unavailable. Please try again later.';
          break;
      }
    }

    console.error('API Error:', errorMessage);
    return throwError(() => errorMessage);
  }

  /**
   * Check if API is available
   */
  isApiAvailable(): Observable<boolean> {
    return new Observable(observer => {
      this.healthCheck().subscribe({
        next: () => {
          observer.next(true);
          observer.complete();
        },
        error: () => {
          observer.next(false);
          observer.complete();
        }
      });
    });
  }
}
