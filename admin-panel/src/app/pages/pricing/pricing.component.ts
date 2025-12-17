import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

type ServiceType = 'website' | 'app' | 'both';
type PlanTier = 'basic' | 'professional' | 'enterprise';

interface PricingPlan {
  id?: number;
  language: string;
  service_type: ServiceType;
  tier: PlanTier;
  name: string;
  slug?: string;
  description: string;
  price_monthly: number;
  price_yearly: number;
  features: string[];
  is_popular: boolean;
  order: number;
  is_active: boolean;
}

interface ServiceCategory {
  id: ServiceType;
  name: string;
  nameAr: string;
  icon: string;
}

@Component({
  selector: 'app-pricing',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './pricing.component.html',
  styleUrls: ['./pricing.component.scss']
})
export class PricingComponent implements OnInit {
  plans: PricingPlan[] = [];
  filteredPlans: PricingPlan[] = [];
  loading = false;
  saving = false;
  showModal = false;
  isEditMode = false;
  selectedLanguage = 'all';
  selectedServiceType: ServiceType | 'all' = 'all';
  selectedTier: PlanTier | 'all' = 'all';
  searchTerm = '';
  
  planForm!: FormGroup;
  currentPlan: PricingPlan | null = null;

  // Service categories
  serviceCategories: ServiceCategory[] = [
    { id: 'website', name: 'Website Development', nameAr: 'تطوير المواقع', icon: '🌐' },
    { id: 'app', name: 'Mobile App Development', nameAr: 'تطوير التطبيقات', icon: '📱' },
    { id: 'both', name: 'Website + App Package', nameAr: 'باقة الموقع والتطبيق', icon: '🚀' }
  ];

  // Plan tiers
  planTiers = [
    { id: 'basic', name: 'Basic', nameAr: 'أساسي' },
    { id: 'professional', name: 'Professional', nameAr: 'احترافي' },
    { id: 'enterprise', name: 'Enterprise', nameAr: 'مؤسسي' }
  ];

  constructor(
    private apiService: ApiService,
    private fb: FormBuilder
  ) {
    this.initForm();
  }

  ngOnInit() {
    this.loadPlans();
  }

  initForm() {
    this.planForm = this.fb.group({
      language: ['en', Validators.required],
      service_type: ['website', Validators.required],
      tier: ['basic', Validators.required],
      name: ['', Validators.required],
      description: ['', Validators.required],
      price_monthly: [0, [Validators.required, Validators.min(0)]],
      price_yearly: [0, [Validators.required, Validators.min(0)]],
      features: this.fb.array([]),
      is_popular: [false],
      is_active: [true],
      order: [0]
    });

    // Add initial feature fields
    this.addFeature();
    this.addFeature();
    this.addFeature();
  }

  get features(): FormArray {
    return this.planForm.get('features') as FormArray;
  }

  addFeature() {
    this.features.push(this.fb.control('', Validators.required));
  }

  removeFeature(index: number) {
    if (this.features.length > 1) {
      this.features.removeAt(index);
    }
  }

  loadPlans() {
    this.loading = true;
    this.apiService.get('/admin/pricing').subscribe({
      next: (response: any) => {
        console.log('Pricing API Response:', response);
        
        // Handle different response formats
        if (response.data) {
          if (response.data.data) {
            this.plans = response.data.data;
          } else {
            this.plans = response.data;
          }
        } else if (Array.isArray(response)) {
          this.plans = response;
        } else {
          this.plans = [];
        }
        
        console.log('Loaded plans:', this.plans);
        this.applyFilters();
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading pricing plans:', error);
        this.plans = [];
        this.filteredPlans = [];
        this.loading = false;
      }
    });
  }

  refreshData() {
    this.loadPlans();
  }

  filterByLanguage(language: string) {
    this.selectedLanguage = language;
    this.applyFilters();
  }

  filterByServiceType(type: ServiceType | 'all') {
    this.selectedServiceType = type;
    this.applyFilters();
  }

  filterByTier(tier: PlanTier | 'all') {
    this.selectedTier = tier;
    this.applyFilters();
  }

  onSearch() {
    this.applyFilters();
  }

  applyFilters() {
    let filtered = [...this.plans];

    // Language filter
    if (this.selectedLanguage !== 'all') {
      filtered = filtered.filter(plan => plan.language === this.selectedLanguage);
    }

    // Service type filter
    if (this.selectedServiceType !== 'all') {
      filtered = filtered.filter(plan => plan.service_type === this.selectedServiceType);
    }

    // Tier filter
    if (this.selectedTier !== 'all') {
      filtered = filtered.filter(plan => plan.tier === this.selectedTier);
    }

    // Search filter
    if (this.searchTerm.trim()) {
      const term = this.searchTerm.toLowerCase();
      filtered = filtered.filter(plan => 
        plan.name.toLowerCase().includes(term) ||
        plan.description?.toLowerCase().includes(term)
      );
    }

    // Sort by service_type, tier, and order
    filtered.sort((a, b) => {
      // First by service type
      const serviceOrder = { website: 1, app: 2, both: 3 };
      const serviceCompare = serviceOrder[a.service_type] - serviceOrder[b.service_type];
      if (serviceCompare !== 0) return serviceCompare;

      // Then by tier
      const tierOrder = { basic: 1, professional: 2, enterprise: 3 };
      const tierCompare = tierOrder[a.tier] - tierOrder[b.tier];
      if (tierCompare !== 0) return tierCompare;

      // Finally by order
      return (a.order || 0) - (b.order || 0);
    });

    this.filteredPlans = filtered;
  }

  calculateSavings(plan: PricingPlan): number {
    const monthlyTotal = plan.price_monthly * 12;
    const savings = monthlyTotal - plan.price_yearly;
    return Math.max(0, Math.round(savings * 100) / 100);
  }

  calculateSavingsPercentage(plan: PricingPlan): number {
    const monthlyTotal = plan.price_monthly * 12;
    if (monthlyTotal === 0) return 0;
    const savings = monthlyTotal - plan.price_yearly;
    return Math.round((savings / monthlyTotal) * 100);
  }

  getServiceCategoryName(type: ServiceType): string {
    const category = this.serviceCategories.find(c => c.id === type);
    return category ? category.name : type;
  }

  getTierName(tier: PlanTier): string {
    const tierObj = this.planTiers.find(t => t.id === tier);
    return tierObj ? tierObj.name : tier;
  }

  openAddModal() {
    this.isEditMode = false;
    this.currentPlan = null;
    this.resetForm();
    this.showModal = true;
  }

  editPlan(plan: PricingPlan) {
    this.isEditMode = true;
    this.currentPlan = plan;
    this.populateForm(plan);
    this.showModal = true;
  }

  resetForm() {
    this.planForm.reset({
      language: 'en',
      service_type: 'website',
      tier: 'basic',
      name: '',
      description: '',
      price_monthly: 0,
      price_yearly: 0,
      is_popular: false,
      is_active: true,
      order: 0
    });

    // Clear features array and add default fields
    while (this.features.length > 0) {
      this.features.removeAt(0);
    }
    this.addFeature();
    this.addFeature();
    this.addFeature();
  }

  populateForm(plan: PricingPlan) {
    this.planForm.patchValue({
      language: plan.language,
      service_type: plan.service_type,
      tier: plan.tier,
      name: plan.name,
      description: plan.description,
      price_monthly: plan.price_monthly,
      price_yearly: plan.price_yearly,
      is_popular: plan.is_popular,
      is_active: plan.is_active,
      order: plan.order
    });

    // Populate features
    while (this.features.length > 0) {
      this.features.removeAt(0);
    }
    
    if (plan.features && plan.features.length > 0) {
      plan.features.forEach(feature => {
        this.features.push(this.fb.control(feature, Validators.required));
      });
    } else {
      this.addFeature();
      this.addFeature();
      this.addFeature();
    }
  }

  savePlan() {
    if (this.planForm.invalid) {
      Object.keys(this.planForm.controls).forEach(key => {
        this.planForm.get(key)?.markAsTouched();
      });
      this.features.controls.forEach(control => control.markAsTouched());
      alert('Please fill in all required fields');
      return;
    }

    this.saving = true;
    const formValue = this.planForm.value;
    
    // Filter out empty features
    formValue.features = formValue.features.filter((f: string) => f && f.trim() !== '');

    if (formValue.features.length === 0) {
      alert('Please add at least one feature');
      this.saving = false;
      return;
    }

    // Generate slug from name
    formValue.slug = formValue.name.toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-');

    const request = this.isEditMode && this.currentPlan
      ? this.apiService.put(`/admin/pricing/${this.currentPlan.id}`, formValue)
      : this.apiService.post('/admin/pricing', formValue);

    request.subscribe({
      next: () => {
        this.saving = false;
        this.closeModal();
        this.loadPlans();
        alert(this.isEditMode ? 'Pricing plan updated successfully!' : 'Pricing plan created successfully!');
      },
      error: (error) => {
        console.error('Error saving pricing plan:', error);
        this.saving = false;
        alert('Error saving pricing plan: ' + (error.error?.message || 'Please try again'));
      }
    });
  }

  toggleStatus(plan: PricingPlan) {
    if (!plan.id) return;

    const updatedPlan = { ...plan, is_active: !plan.is_active };
    
    this.apiService.put(`/admin/pricing/${plan.id}`, updatedPlan).subscribe({
      next: () => {
        plan.is_active = !plan.is_active;
        alert('Status updated successfully!');
      },
      error: (error) => {
        console.error('Error updating status:', error);
        alert('Error updating status. Please try again.');
      }
    });
  }

  duplicatePlan(plan: PricingPlan) {
    const newPlan = {
      ...plan,
      id: undefined,
      name: plan.name + ' (Copy)',
      slug: undefined
    };
    
    this.currentPlan = null;
    this.isEditMode = false;
    this.populateForm(newPlan);
    this.showModal = true;
  }

  deletePlan(plan: PricingPlan) {
    if (!plan.id) return;

    if (!confirm(`Are you sure you want to delete "${plan.name}"?`)) {
      return;
    }

    this.apiService.delete(`/admin/pricing/${plan.id}`).subscribe({
      next: () => {
        this.loadPlans();
        alert('Pricing plan deleted successfully!');
      },
      error: (error) => {
        console.error('Error deleting pricing plan:', error);
        alert('Error deleting pricing plan. Please try again.');
      }
    });
  }

  closeModal() {
    this.showModal = false;
    this.resetForm();
  }

  // Helper to get plan count by category
  getPlanCountByCategory(type: ServiceType): number {
    return this.plans.filter(p => p.service_type === type).length;
  }

  // Helper to get plan count by tier
  getPlanCountByTier(tier: PlanTier): number {
    return this.plans.filter(p => p.tier === tier).length;
  }

  // Clear search term
  clearSearch() {
    this.searchTerm = '';
    this.applyFilters();
  }

  // Clear all filters
  clearAllFilters() {
    this.searchTerm = '';
    this.selectedLanguage = 'all';
    this.selectedServiceType = 'all';
    this.selectedTier = 'all';
    this.applyFilters();
  }

  // Get plans count by language
  getPlansByLanguage(language: string): number {
    return this.plans.filter(p => p.language === language).length;
  }

  // Deactivate all plans
  deactivateAll() {
    if (!confirm('Are you sure you want to deactivate all pricing plans?')) {
      return;
    }

    this.loading = true;
    const updatePromises = this.plans
      .filter(p => p.is_active && p.id)
      .map(p => this.apiService.put(`/admin/pricing/${p.id}`, { ...p, is_active: false }).toPromise());

    Promise.all(updatePromises).then(() => {
      this.loading = false;
      this.loadPlans();
      alert('All plans deactivated successfully!');
    }).catch(error => {
      console.error('Error deactivating plans:', error);
      this.loading = false;
      alert('Error deactivating plans. Please try again.');
    });
  }

  // Activate all plans
  activateAll() {
    if (!confirm('Are you sure you want to activate all pricing plans?')) {
      return;
    }

    this.loading = true;
    const updatePromises = this.plans
      .filter(p => !p.is_active && p.id)
      .map(p => this.apiService.put(`/admin/pricing/${p.id}`, { ...p, is_active: true }).toPromise());

    Promise.all(updatePromises).then(() => {
      this.loading = false;
      this.loadPlans();
      alert('All plans activated successfully!');
    }).catch(error => {
      console.error('Error activating plans:', error);
      this.loading = false;
      alert('Error activating plans. Please try again.');
    });
  }

  // Export plans to JSON
  exportPlans() {
    const dataStr = JSON.stringify(this.plans, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `pricing-plans-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
  }

  // Get active plans by category
  getActivePlansByCategory(type: ServiceType): number {
    return this.plans.filter(p => p.service_type === type && p.is_active).length;
  }

  // Get popular plans count
  getPopularPlansCount(): number {
    return this.plans.filter(p => p.is_popular).length;
  }
}
