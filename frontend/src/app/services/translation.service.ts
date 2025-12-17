import { Injectable } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TranslationService {
  private currentLanguageSubject = new BehaviorSubject<string>('en');
  public currentLanguage$: Observable<string> = this.currentLanguageSubject.asObservable();

  constructor(private translate: TranslateService) {
    this.initializeLanguage();
  }

  /**
   * Initialize language from localStorage or default
   */
  private initializeLanguage(): void {
    const savedLanguage = localStorage.getItem('language') || 'en';
    this.setLanguage(savedLanguage);
  }

  /**
   * Set current language
   */
  setLanguage(language: string): void {
    if (language !== 'en' && language !== 'ar') {
      language = 'en';
    }

    this.translate.use(language);
    this.currentLanguageSubject.next(language);
    localStorage.setItem('language', language);

    // Set document direction for RTL
    document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = language;
  }

  /**
   * Get current language
   */
  getCurrentLanguage(): string {
    return this.currentLanguageSubject.value;
  }

  /**
   * Toggle between English and Arabic
   */
  toggleLanguage(): void {
    const newLanguage = this.getCurrentLanguage() === 'en' ? 'ar' : 'en';
    this.setLanguage(newLanguage);
  }

  /**
   * Check if current language is RTL
   */
  isRTL(): boolean {
    return this.getCurrentLanguage() === 'ar';
  }

  /**
   * Translate a key
   */
  instant(key: string): string {
    return this.translate.instant(key);
  }
}
