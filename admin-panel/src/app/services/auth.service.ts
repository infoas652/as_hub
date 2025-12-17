import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { BehaviorSubject, Observable, tap, catchError, throwError, timeout } from 'rxjs';
import { environment } from '../../environments/environment';

interface LoginResponse {
  access_token: string;
  token_type: string;
  expires_in: number;
  user?: User;
}

interface User {
  id: number;
  name: string;
  email: string;
  role?: string;
}

interface AuthError {
  message: string;
  status: number;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  private tokenKey = 'auth_token';
  private userKey = 'auth_user';
  private readonly timeout = environment.apiTimeout || 30000;
  
  private userSubject = new BehaviorSubject<User | null>(this.getUserFromStorage());
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(this.hasValidToken());
  
  public user$ = this.userSubject.asObservable();
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

  constructor(
    private http: HttpClient,
    private router: Router
  ) {
    // Load user on initialization if token exists
    if (this.hasValidToken()) {
      this.loadUser();
    }
  }

  /**
   * Login with email and password
   */
  login(email: string, password: string): Observable<LoginResponse> {
    // Validate inputs
    if (!email || !password) {
      return throwError(() => ({
        message: 'Email and password are required',
        status: 400
      }));
    }

    return this.http.post<LoginResponse>(`${this.apiUrl}/auth/login`, { email, password })
      .pipe(
        timeout(this.timeout),
        tap(response => {
          this.setToken(response.access_token);
          if (response.user) {
            this.setUser(response.user);
          } else {
            this.loadUser();
          }
          this.isAuthenticatedSubject.next(true);
        }),
        catchError(this.handleError.bind(this))
      );
  }

  /**
   * Logout and clear session
   */
  logout(): void {
    const token = this.getToken();
    
    if (token) {
      this.http.post(`${this.apiUrl}/auth/logout`, {})
        .pipe(timeout(5000))
        .subscribe({
          complete: () => this.clearSession(),
          error: () => this.clearSession()
        });
    } else {
      this.clearSession();
    }
  }

  /**
   * Refresh authentication token
   */
  refreshToken(): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}/auth/refresh`, {})
      .pipe(
        timeout(this.timeout),
        tap(response => {
          this.setToken(response.access_token);
        }),
        catchError(error => {
          this.clearSession();
          return throwError(() => error);
        })
      );
  }

  /**
   * Get current authentication token
   */
  getToken(): string | null {
    return sessionStorage.getItem(this.tokenKey);
  }

  /**
   * Set authentication token
   */
  setToken(token: string): void {
    sessionStorage.setItem(this.tokenKey, token);
  }

  /**
   * Clear authentication token
   */
  clearToken(): void {
    sessionStorage.removeItem(this.tokenKey);
  }

  /**
   * Check if user is authenticated
   */
  isAuthenticated(): boolean {
    return this.hasValidToken();
  }

  /**
   * Load current user from API
   */
  private loadUser(): void {
    if (!this.hasValidToken()) {
      return;
    }

    this.http.get<User>(`${this.apiUrl}/auth/me`)
      .pipe(
        timeout(this.timeout),
        catchError(error => {
          console.error('Failed to load user:', error);
          this.clearSession();
          return throwError(() => error);
        })
      )
      .subscribe({
        next: (user) => this.setUser(user),
        error: () => this.clearSession()
      });
  }

  /**
   * Get current user
   */
  getUser(): User | null {
    return this.userSubject.value;
  }

  /**
   * Set user data
   */
  private setUser(user: User): void {
    this.userSubject.next(user);
    sessionStorage.setItem(this.userKey, JSON.stringify(user));
  }

  /**
   * Get user from storage
   */
  private getUserFromStorage(): User | null {
    const userJson = sessionStorage.getItem(this.userKey);
    if (userJson) {
      try {
        return JSON.parse(userJson);
      } catch {
        return null;
      }
    }
    return null;
  }

  /**
   * Check if token exists and is valid
   */
  private hasValidToken(): boolean {
    const token = this.getToken();
    return !!token && token.length > 0;
  }

  /**
   * Clear all session data
   */
  private clearSession(): void {
    this.clearToken();
    sessionStorage.removeItem(this.userKey);
    this.userSubject.next(null);
    this.isAuthenticatedSubject.next(false);
    this.router.navigate(['/login']);
  }

  /**
   * Handle authentication errors
   */
  private handleError(error: HttpErrorResponse): Observable<never> {
    let errorMessage: AuthError = {
      message: 'Authentication failed',
      status: error.status
    };

    if (error.error instanceof ErrorEvent) {
      // Client-side error
      errorMessage.message = `Connection error: ${error.error.message}`;
    } else {
      // Server-side error
      switch (error.status) {
        case 0:
          errorMessage.message = 'Unable to connect to server. Please check your internet connection.';
          break;
        case 401:
          errorMessage.message = error.error?.message || 'Invalid email or password.';
          break;
        case 403:
          errorMessage.message = 'Access denied. You do not have permission to login.';
          break;
        case 422:
          errorMessage.message = error.error?.message || 'Invalid credentials provided.';
          break;
        case 429:
          errorMessage.message = 'Too many login attempts. Please try again later.';
          break;
        case 500:
          errorMessage.message = 'Server error. Please try again later.';
          break;
        case 503:
          errorMessage.message = 'Service temporarily unavailable. Please try again later.';
          break;
        default:
          errorMessage.message = error.error?.message || 'Authentication failed. Please try again.';
      }
    }

    console.error('Auth Error:', errorMessage);
    return throwError(() => errorMessage);
  }

  /**
   * Update user profile
   */
  updateProfile(data: Partial<User>): Observable<User> {
    return this.http.put<User>(`${this.apiUrl}/auth/profile`, data)
      .pipe(
        timeout(this.timeout),
        tap(user => this.setUser(user)),
        catchError(this.handleError.bind(this))
      );
  }

  /**
   * Change password
   */
  changePassword(currentPassword: string, newPassword: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/change-password`, {
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPassword
    }).pipe(
      timeout(this.timeout),
      catchError(this.handleError.bind(this))
    );
  }
}
