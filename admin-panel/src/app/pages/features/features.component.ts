import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { Subject } from 'rxjs';
import { debounceTime, distinctUntilChanged } from 'rxjs/operators';

interface Feature {
  id?: number;
  language: string;
  title: string;
  description: string;
  icon: string;
  order: number;
  is_active: boolean;
}

@Component({
  selector: 'app-features',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  templateUrl: './features.component.html',
  styleUrls: ['./features.component.scss']
})
export class FeaturesComponent implements OnInit, OnDestroy {
  features: Feature[] = [];
  filteredFeatures: Feature[] = [];
  loading: boolean = true;
  showModal: boolean = false;
  isEditMode: boolean = false;
  searchTerm: string = '';
  selectedLanguage: string = 'all';
  
  // Performance optimization
  private searchSubject = new Subject<string>();
  private destroy$ = new Subject<void>();

  currentFeature: Feature = {
    language: 'en',
    title: '',
    description: '',
    icon: '',
    order: 0,
    is_active: true
  };

  // Icon options for features
  iconOptions = [
    { value: 'bi-lightning-charge', label: 'Lightning' },
    { value: 'bi-shield-check', label: 'Shield' },
    { value: 'bi-graph-up', label: 'Graph' },
    { value: 'bi-gear', label: 'Gear' },
    { value: 'bi-people', label: 'People' },
    { value: 'bi-clock', label: 'Clock' },
    { value: 'bi-star', label: 'Star' },
    { value: 'bi-heart', label: 'Heart' },
    { value: 'bi-trophy', label: 'Trophy' },
    { value: 'bi-rocket', label: 'Rocket' },
    { value: 'bi-cpu', label: 'CPU' },
    { value: 'bi-cloud', label: 'Cloud' },
    { value: 'bi-database', label: 'Database' },
    { value: 'bi-lock', label: 'Lock' },
    { value: 'bi-speedometer', label: 'Speedometer' }
  ];

  constructor(
    private apiService: ApiService,
    private translate: TranslateService
  ) {
    // Setup debounced search
    this.searchSubject.pipe(
      debounceTime(300),
      distinctUntilChanged()
    ).subscribe(() => {
      this.applyFilters();
    });
  }

  ngOnInit(): void {
    this.loadFeatures();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadFeatures(): void {
    this.loading = true;
    console.log('🔄 Loading features...');
    
    this.apiService.getFeatures().subscribe({
      next: (response: any) => {
        console.log('✅ Features loaded:', response);
        
        // Handle different response formats
        if (response.data) {
          // Paginated response
          if (Array.isArray(response.data.data)) {
            this.features = response.data.data;
          } 
          // Direct array in data
          else if (Array.isArray(response.data)) {
            this.features = response.data;
          }
        } 
        // Direct array response
        else if (Array.isArray(response)) {
          this.features = response;
        }
        
        this.filteredFeatures = [...this.features];
        this.applyFilters();
        this.loading = false;
        
        console.log('📊 Total features:', this.features.length);
      },
      error: (error) => {
        console.error('❌ Error loading features:', error);
        this.loading = false;
        alert('Failed to load features. Please check console for details.');
      }
    });
  }

  applyFilters(): void {
    let filtered = [...this.features];

    // Filter by language
    if (this.selectedLanguage !== 'all') {
      filtered = filtered.filter(f => f.language === this.selectedLanguage);
    }

    // Filter by search term
    if (this.searchTerm.trim()) {
      const search = this.searchTerm.toLowerCase();
      filtered = filtered.filter(f =>
        f.title.toLowerCase().includes(search) ||
        f.description.toLowerCase().includes(search)
      );
    }

    this.filteredFeatures = filtered;
  }

  onLanguageChange(): void {
    this.applyFilters();
  }

  onSearch(): void {
    this.searchSubject.next(this.searchTerm);
  }

  // TrackBy function for performance
  trackByFeatureId(index: number, feature: Feature): number {
    return feature.id || index;
  }

  // TrackBy function for icon options
  trackByIconValue(index: number, icon: any): string {
    return icon.value;
  }

  openAddModal(): void {
    this.isEditMode = false;
    this.currentFeature = {
      language: 'en',
      title: '',
      description: '',
      icon: 'bi-lightning-charge',
      order: this.features.length,
      is_active: true
    };
    this.showModal = true;
  }

  openEditModal(feature: Feature): void {
    this.isEditMode = true;
    this.currentFeature = { ...feature };
    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
    this.currentFeature = {
      language: 'en',
      title: '',
      description: '',
      icon: '',
      order: 0,
      is_active: true
    };
  }

  saveFeature(): void {
    // Validation
    if (!this.currentFeature.title.trim()) {
      alert(this.translate.instant('features.titleRequired'));
      return;
    }

    if (!this.currentFeature.description.trim()) {
      alert(this.translate.instant('features.descriptionRequired'));
      return;
    }

    console.log('💾 Saving feature:', this.currentFeature);

    if (this.isEditMode && this.currentFeature.id) {
      // Update existing feature
      this.apiService.updateFeature(this.currentFeature.id, this.currentFeature).subscribe({
        next: (response) => {
          console.log('✅ Feature updated:', response);
          alert(this.translate.instant('features.updateSuccess'));
          this.closeModal();
          this.loadFeatures();
        },
        error: (error) => {
          console.error('❌ Error updating feature:', error);
          alert(this.translate.instant('features.updateError'));
        }
      });
    } else {
      // Create new feature
      this.apiService.createFeature(this.currentFeature).subscribe({
        next: (response) => {
          console.log('✅ Feature created:', response);
          alert(this.translate.instant('features.createSuccess'));
          this.closeModal();
          this.loadFeatures();
        },
        error: (error) => {
          console.error('❌ Error creating feature:', error);
          alert(this.translate.instant('features.createError'));
        }
      });
    }
  }

  toggleStatus(feature: Feature): void {
    if (!feature.id) return;

    console.log('🔄 Toggling feature status:', feature.id);

    this.apiService.toggleFeature(feature.id).subscribe({
      next: (response) => {
        console.log('✅ Feature status toggled:', response);
        this.loadFeatures();
      },
      error: (error) => {
        console.error('❌ Error toggling feature:', error);
        alert(this.translate.instant('features.toggleError'));
      }
    });
  }

  deleteFeature(feature: Feature): void {
    if (!feature.id) return;

    const confirmMessage = this.translate.instant('features.deleteConfirm', { title: feature.title });
    if (!confirm(confirmMessage)) return;

    console.log('🗑️ Deleting feature:', feature.id);

    this.apiService.deleteFeature(feature.id).subscribe({
      next: (response) => {
        console.log('✅ Feature deleted:', response);
        alert(this.translate.instant('features.deleteSuccess'));
        this.loadFeatures();
      },
      error: (error) => {
        console.error('❌ Error deleting feature:', error);
        alert(this.translate.instant('features.deleteError'));
      }
    });
  }

  getIconClass(icon: string): string {
    return icon || 'bi-star';
  }

  getActiveCount(): number {
    return this.features.filter(f => f.is_active).length;
  }

  getInactiveCount(): number {
    return this.features.filter(f => !f.is_active).length;
  }
}
