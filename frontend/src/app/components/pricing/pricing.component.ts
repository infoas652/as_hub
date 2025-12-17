import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

interface PricingPlan {
  id: number;
  service_type: ServiceType;
  tier: string;
  name: string;
  description: string;
  price_monthly: number;
  price_yearly: number;
  features: string[];
  is_popular: boolean;
  savings?: number;
  savings_percentage?: number;
}

type ServiceType = 'website' | 'app' | 'both';
type BillingPeriod = 'monthly' | 'yearly';

interface PricingCategory {
  id: ServiceType;
  name: string;
  nameAr: string;
  icon: string;
  description: string;
  descriptionAr: string;
  plans: PricingPlan[];
}

@Component({
  selector: 'app-pricing',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  templateUrl: './pricing.component.html',
  styleUrls: ['./pricing.component.scss']
})
export class PricingComponent implements OnInit, OnDestroy {
  // Service type selection
  selectedServiceType: ServiceType = 'website';
  
  // Billing period
  billingPeriod: BillingPeriod = 'monthly';
  isYearly: boolean = false;
  
  // Data
  pricingCategories: PricingCategory[] = [];
  loading = true;
  currentLanguage: string = 'en';
  
  private destroy$ = new Subject<void>();

  // Category metadata
  private categoryMetadata = {
    website: {
      name: 'Website Development',
      nameAr: 'تطوير المواقع',
      icon: '🌐',
      description: 'Professional websites tailored to your business',
      descriptionAr: 'مواقع احترافية مصممة خصيصاً لعملك'
    },
    app: {
      name: 'Mobile App Development',
      nameAr: 'تطوير التطبيقات',
      icon: '📱',
      description: 'Native and cross-platform mobile applications',
      descriptionAr: 'تطبيقات موبايل أصلية ومتعددة المنصات'
    },
    both: {
      name: 'Website + App Package',
      nameAr: 'باقة الموقع والتطبيق',
      icon: '🚀',
      description: 'Complete digital solution for your business',
      descriptionAr: 'حل رقمي متكامل لعملك'
    }
  };

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
        this.loadPricing();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadPricing(): void {
    this.loading = true;
    console.log('🔄 Loading pricing for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Pricing API response:', response);
        
        if (response.data?.pricing_by_service) {
          // Use the new grouped format from API
          this.pricingCategories = this.mapApiDataToCategories(response.data.pricing_by_service);
        } else if (response.data?.pricing && response.data.pricing.length > 0) {
          // Fallback: group the flat pricing array by service_type
          this.pricingCategories = this.groupPricingByServiceType(response.data.pricing);
        } else {
          // No data from API, use empty categories
          this.pricingCategories = this.createEmptyCategories();
        }
        
        console.log('📊 Processed pricing categories:', this.pricingCategories);
        this.loading = false;
      },
      error: (error) => {
        console.error('❌ Error loading pricing:', error);
        this.pricingCategories = this.createEmptyCategories();
        this.loading = false;
      }
    });
  }

  private mapApiDataToCategories(pricingByService: any): PricingCategory[] {
    const categories: PricingCategory[] = [];
    
    for (const [serviceType, plans] of Object.entries(pricingByService)) {
      const metadata = this.categoryMetadata[serviceType as ServiceType];
      if (metadata && Array.isArray(plans)) {
        categories.push({
          id: serviceType as ServiceType,
          ...metadata,
          plans: this.sortPlansByTier(plans as PricingPlan[])
        });
      }
    }
    
    return categories;
  }

  private groupPricingByServiceType(pricing: PricingPlan[]): PricingCategory[] {
    const grouped: { [key: string]: PricingPlan[] } = {
      website: [],
      app: [],
      both: []
    };
    
    pricing.forEach(plan => {
      if (grouped[plan.service_type]) {
        grouped[plan.service_type].push(plan);
      }
    });
    
    return Object.entries(grouped).map(([serviceType, plans]) => {
      const metadata = this.categoryMetadata[serviceType as ServiceType];
      return {
        id: serviceType as ServiceType,
        ...metadata,
        plans: this.sortPlansByTier(plans)
      };
    });
  }

  private sortPlansByTier(plans: PricingPlan[]): PricingPlan[] {
    const tierOrder = { basic: 1, professional: 2, enterprise: 3 };
    return plans.sort((a, b) => {
      const orderA = tierOrder[a.tier as keyof typeof tierOrder] || 999;
      const orderB = tierOrder[b.tier as keyof typeof tierOrder] || 999;
      return orderA - orderB;
    });
  }

  private createEmptyCategories(): PricingCategory[] {
    return Object.entries(this.categoryMetadata).map(([id, metadata]) => ({
      id: id as ServiceType,
      ...metadata,
      plans: []
    }));
  }

  selectServiceType(type: ServiceType): void {
    this.selectedServiceType = type;
  }

  toggleBilling(): void {
    this.isYearly = !this.isYearly;
    this.billingPeriod = this.isYearly ? 'yearly' : 'monthly';
  }

  getCurrentCategory(): PricingCategory | undefined {
    return this.pricingCategories.find(cat => cat.id === this.selectedServiceType);
  }

  getPrice(plan: PricingPlan): number {
    return this.billingPeriod === 'monthly' ? plan.price_monthly : plan.price_yearly;
  }

  getSavings(plan: PricingPlan): number {
    // Use savings_percentage from API if available
    if (plan.savings_percentage) {
      return plan.savings_percentage;
    }
    
    // Calculate if not provided
    const yearlyTotal = plan.price_monthly * 12;
    const savings = yearlyTotal - plan.price_yearly;
    return Math.round((savings / yearlyTotal) * 100);
  }

  getSavingsAmount(plan: PricingPlan): number {
    // Use savings from API if available
    if (plan.savings) {
      return plan.savings;
    }
    
    // Calculate if not provided
    const yearlyTotal = plan.price_monthly * 12;
    return Math.max(0, yearlyTotal - plan.price_yearly);
  }

  getCategoryName(category: PricingCategory): string {
    return this.currentLanguage === 'ar' ? category.nameAr : category.name;
  }

  getCategoryDescription(category: PricingCategory): string {
    return this.currentLanguage === 'ar' ? category.descriptionAr : category.description;
  }

  scrollToContact(): void {
    const element = document.querySelector('#contact');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // Helper to check if category has plans
  hasPlans(category: PricingCategory): boolean {
    return category.plans && category.plans.length > 0;
  }

  // Get all categories for display
  getAllCategories(): PricingCategory[] {
    return this.pricingCategories;
  }
}
