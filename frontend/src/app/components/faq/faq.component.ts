import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

interface FaqItem {
  id: number;
  question: string;
  answer: string;
  category?: string;
  isOpen?: boolean;
}

@Component({
  selector: 'app-faq',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './faq.component.html',
  styleUrls: ['./faq.component.scss']
})
export class FaqComponent implements OnInit, OnDestroy {
  faqs: FaqItem[] = [];
  loading = true;
  activeIndex: number | null = null;
  selectedCategory = 'all';
  categories: string[] = ['all'];
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
        this.loadFaqs();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadFaqs(): void {
    this.loading = true;
    console.log('🔄 Loading FAQs for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ FAQ API response:', response);
        
        // Handle different response formats
        let faqData: FaqItem[] = [];
        if (response.data) {
          faqData = response.data.faq || [];
        } else {
          faqData = response.faq || [];
        }
        
        this.faqs = faqData.map((faq: FaqItem) => ({
          ...faq,
          isOpen: false
        }));
        
        this.extractCategories();
        this.loading = false;
        console.log('📊 Total FAQs loaded:', this.faqs.length);
      },
      error: (error) => {
        console.error('❌ Error loading FAQs:', error);
        this.loading = false;
        // No fallback - show empty state
        this.faqs = [];
        this.extractCategories();
      }
    });
  }

  extractCategories(): void {
    const uniqueCategories = new Set(
      this.faqs
        .map(faq => faq.category)
        .filter(cat => cat !== undefined) as string[]
    );
    this.categories = ['all', ...Array.from(uniqueCategories)];
  }

  toggleFaq(index: number): void {
    this.activeIndex = this.activeIndex === index ? null : index;
  }

  filterByCategory(category: string): void {
    this.selectedCategory = category;
  }

  get filteredFaqs(): FaqItem[] {
    if (this.selectedCategory === 'all') {
      return this.faqs;
    }
    return this.faqs.filter(faq => faq.category === this.selectedCategory);
  }
}
