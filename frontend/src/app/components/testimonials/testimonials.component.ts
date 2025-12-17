import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

interface Testimonial {
  id: number;
  client_name: string;
  client_position: string;
  client_company: string;
  client_avatar: string;
  testimonial: string;
  rating: number;
}

@Component({
  selector: 'app-testimonials',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './testimonials.component.html',
  styleUrls: ['./testimonials.component.scss']
})
export class TestimonialsComponent implements OnInit, OnDestroy {
  testimonials: Testimonial[] = [];
  loading = true;
  currentIndex = 0;
  currentLanguage: string = 'en';
  private destroy$ = new Subject<void>();
  private autoSlideInterval: any;

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
        this.loadTestimonials();
      });
    
    this.startAutoSlide();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
    if (this.autoSlideInterval) {
      clearInterval(this.autoSlideInterval);
    }
  }

  loadTestimonials(): void {
    this.loading = true;
    console.log('🔄 Loading testimonials for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Testimonials API response:', response);
        
        // Handle different response formats
        if (response.data) {
          this.testimonials = response.data.testimonials || [];
        } else {
          this.testimonials = response.testimonials || [];
        }
        
        this.loading = false;
        console.log('📊 Total testimonials loaded:', this.testimonials.length);
        
        // Reset current index if needed
        if (this.currentIndex >= this.testimonials.length) {
          this.currentIndex = 0;
        }
      },
      error: (error) => {
        console.error('❌ Error loading testimonials:', error);
        this.loading = false;
        // No fallback - show empty state
        this.testimonials = [];
      }
    });
  }

  startAutoSlide(): void {
    this.autoSlideInterval = setInterval(() => {
      if (this.testimonials.length > 0) {
        this.nextSlide();
      }
    }, 5000);
  }

  nextSlide(): void {
    if (this.testimonials.length > 0) {
      this.currentIndex = (this.currentIndex + 1) % this.testimonials.length;
    }
  }

  prevSlide(): void {
    if (this.testimonials.length > 0) {
      this.currentIndex = this.currentIndex === 0 
        ? this.testimonials.length - 1 
        : this.currentIndex - 1;
    }
  }

  goToSlide(index: number): void {
    this.currentIndex = index;
  }

  getStars(rating: number): number[] {
    return Array(rating).fill(0);
  }

  getInitials(name: string): string {
    if (!name) return '?';
    const parts = name.trim().split(' ');
    if (parts.length === 1) {
      return parts[0].charAt(0).toUpperCase();
    }
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
  }
}
