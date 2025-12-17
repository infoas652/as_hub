import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

interface Service {
  id: number;
  title: string;
  description: string;
  icon: string;
  features: string[];
}

@Component({
  selector: 'app-services',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './services.component.html',
  styleUrls: ['./services.component.scss']
})
export class ServicesComponent implements OnInit, OnDestroy {
  services: Service[] = [];
  loading = true;
  currentLanguage: string = 'en';
  private destroy$ = new Subject<void>();

  constructor(
    private apiService: ApiService,
    private translationService: TranslationService
  ) {}

  ngOnInit(): void {
    // Subscribe to language changes
    this.translationService.currentLanguage$
      .pipe(takeUntil(this.destroy$))
      .subscribe(lang => {
        this.currentLanguage = lang;
        this.loadServices();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadServices(): void {
    this.loading = true;
    console.log('🔄 Loading services for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Services API response:', response);
        
        // Handle different response formats
        if (response.data) {
          this.services = response.data.services || [];
        } else {
          this.services = response.services || [];
        }
        
        this.loading = false;
        console.log('📊 Total services loaded:', this.services.length);
      },
      error: (error) => {
        console.error('❌ Error loading services:', error);
        this.loading = false;
        // No fallback - show empty state
        this.services = [];
      }
    });
  }

  getIconClass(icon: string): string {
    // Map icon names to emoji or icon classes
    const iconMap: { [key: string]: string } = {
      'website': '🌐',
      'mobile': '📱',
      'package': '📦',
      'ecommerce': '🛒',
      'management': '⚙️',
      'bi-globe': '🌐',
      'bi-phone': '📱',
      'bi-box': '📦',
      'bi-cart': '🛒',
      'bi-gear': '⚙️',
      'default': '💼'
    };
    return iconMap[icon] || iconMap['default'];
  }
}
