import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-hero',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './hero.component.html',
  styleUrls: ['./hero.component.scss']
})
export class HeroComponent implements OnInit, OnDestroy {
  heroTitle = '';
  heroSubtitle = '';
  heroDescription = '';
  ctaDemo = '';
  ctaStart = '';
  pricingHint = '';
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
        this.loadHeroContent();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadHeroContent(): void {
    this.loading = true;
    console.log('🔄 Loading hero content for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Hero content API response:', response);
        
        // Get settings from response
        let settings: any = {};
        if (response.data) {
          settings = response.data.settings || {};
        } else {
          settings = response.settings || {};
        }
        
        // Load hero content from settings
        this.heroTitle = settings.hero_title || '';
        this.heroSubtitle = settings.hero_subtitle || '';
        this.heroDescription = settings.hero_description || '';
        this.ctaDemo = settings.hero_cta_demo || '';
        this.ctaStart = settings.hero_cta_start || '';
        this.pricingHint = settings.hero_pricing_hint || '';
        
        this.loading = false;
        console.log('📊 Hero content loaded');
      },
      error: (error) => {
        console.error('❌ Error loading hero content:', error);
        this.loading = false;
        // No fallback - show empty
        this.heroTitle = '';
        this.heroSubtitle = '';
        this.heroDescription = '';
        this.ctaDemo = '';
        this.ctaStart = '';
        this.pricingHint = '';
      }
    });
  }

  scrollToContact(): void {
    const element = document.querySelector('#contact');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  scrollToPricing(): void {
    const element = document.querySelector('#pricing');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
}
