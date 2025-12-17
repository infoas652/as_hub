import { Injectable } from '@angular/core';
import { HttpClient, HttpParams, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError, timeout, retry, catchError } from 'rxjs';
import { environment } from '../../environments/environment';

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

  // Generic CRUD methods with error handling
  get<T>(endpoint: string, params?: any): Observable<T> {
    let httpParams = new HttpParams();
    if (params) {
      Object.keys(params).forEach(key => {
        if (params[key] !== null && params[key] !== undefined) {
          httpParams = httpParams.append(key, params[key]);
        }
      });
    }
    return this.http.get<T>(`${this.apiUrl}${endpoint}`, { params: httpParams })
      .pipe(
        timeout(this.timeout),
        retry(this.maxRetries),
        catchError(this.handleError)
      );
  }

  post<T>(endpoint: string, data: any): Observable<T> {
    return this.http.post<T>(`${this.apiUrl}${endpoint}`, data)
      .pipe(
        timeout(this.timeout),
        catchError(this.handleError)
      );
  }

  put<T>(endpoint: string, data: any): Observable<T> {
    return this.http.put<T>(`${this.apiUrl}${endpoint}`, data)
      .pipe(
        timeout(this.timeout),
        catchError(this.handleError)
      );
  }

  delete<T>(endpoint: string): Observable<T> {
    return this.http.delete<T>(`${this.apiUrl}${endpoint}`)
      .pipe(
        timeout(this.timeout),
        catchError(this.handleError)
      );
  }

  // Services
  getServices(language: string = 'en'): Observable<any> {
    return this.get('/admin/services', { language });
  }

  createService(data: any): Observable<any> {
    return this.post('/admin/services', data);
  }

  updateService(id: number, data: any): Observable<any> {
    return this.put(`/admin/services/${id}`, data);
  }

  deleteService(id: number): Observable<any> {
    return this.delete(`/admin/services/${id}`);
  }

  // Pricing
  getPricing(language: string = 'en'): Observable<any> {
    return this.get('/admin/pricing', { language });
  }

  createPricing(data: any): Observable<any> {
    return this.post('/admin/pricing', data);
  }

  updatePricing(id: number, data: any): Observable<any> {
    return this.put(`/admin/pricing/${id}`, data);
  }

  deletePricing(id: number): Observable<any> {
    return this.delete(`/admin/pricing/${id}`);
  }

  // Features
  getFeatures(language: string = 'en'): Observable<any> {
    return this.get('/admin/features', { language });
  }

  createFeature(data: any): Observable<any> {
    return this.post('/admin/features', data);
  }

  updateFeature(id: number, data: any): Observable<any> {
    return this.put(`/admin/features/${id}`, data);
  }

  deleteFeature(id: number): Observable<any> {
    return this.delete(`/admin/features/${id}`);
  }

  toggleFeature(id: number): Observable<any> {
    return this.post(`/admin/features/${id}/toggle`, {});
  }

  // Testimonials
  getTestimonials(language: string = 'en'): Observable<any> {
    return this.get('/admin/testimonials', { language });
  }

  createTestimonial(data: any): Observable<any> {
    return this.post('/admin/testimonials', data);
  }

  updateTestimonial(id: number, data: any): Observable<any> {
    return this.put(`/admin/testimonials/${id}`, data);
  }

  deleteTestimonial(id: number): Observable<any> {
    return this.delete(`/admin/testimonials/${id}`);
  }

  // FAQ
  getFaq(language: string = 'en'): Observable<any> {
    return this.get('/admin/faq', { language });
  }

  createFaq(data: any): Observable<any> {
    return this.post('/admin/faq', data);
  }

  updateFaq(id: number, data: any): Observable<any> {
    return this.put(`/admin/faq/${id}`, data);
  }

  deleteFaq(id: number): Observable<any> {
    return this.delete(`/admin/faq/${id}`);
  }

  // Leads
  getLeads(): Observable<any> {
    return this.get('/admin/leads');
  }

  updateLead(id: number, data: any): Observable<any> {
    return this.put(`/admin/leads/${id}`, data);
  }

  deleteLead(id: number): Observable<any> {
    return this.delete(`/admin/leads/${id}`);
  }

  exportLeads(): Observable<Blob> {
    return this.http.get(`${this.apiUrl}/admin/leads/export`, { responseType: 'blob' })
      .pipe(
        timeout(this.timeout),
        catchError(this.handleError)
      );
  }

  // Settings
  getSettings(): Observable<any> {
    return this.get('/admin/settings');
  }

  updateSettings(data: any): Observable<any> {
    return this.put('/admin/settings', data);
  }

  // Media
  getMedia(): Observable<any> {
    return this.get('/admin/media');
  }

  uploadMedia(file: File, altText?: string, title?: string): Observable<any> {
    const formData = new FormData();
    formData.append('file', file);
    if (altText) formData.append('alt_text', altText);
    if (title) formData.append('title', title);
    
    return this.http.post(`${this.apiUrl}/admin/media-upload`, formData)
      .pipe(
        timeout(60000), // Longer timeout for file uploads
        catchError(this.handleError)
      );
  }

  deleteMedia(id: number): Observable<any> {
    return this.delete(`/admin/media/${id}`);
  }

  // Dashboard Stats
  getDashboardStats(): Observable<any> {
    return this.get('/admin/dashboard/stats');
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
    } else if (error.error instanceof Blob) {
      // Handle blob errors (from file downloads)
      return new Observable(observer => {
        const reader = new FileReader();
        reader.onload = () => {
          try {
            const errorObj = JSON.parse(reader.result as string);
            observer.error({
              message: errorObj.message || 'File operation failed',
              status: error.status,
              error: errorObj
            });
          } catch {
            observer.error({
              message: 'File operation failed',
              status: error.status
            });
          }
        };
        reader.readAsText(error.error);
      });
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
          errorMessage.message = 'Access denied. You do not have permission to perform this action.';
          break;
        case 404:
          errorMessage.message = 'Resource not found.';
          break;
        case 422:
          errorMessage.message = error.error?.message || 'Validation error. Please check your input.';
          if (error.error?.errors) {
            // Laravel validation errors
            const validationErrors = Object.values(error.error.errors).flat();
            errorMessage.message = validationErrors.join(', ');
          }
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
      this.http.get(`${this.apiUrl}/health`).subscribe({
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
