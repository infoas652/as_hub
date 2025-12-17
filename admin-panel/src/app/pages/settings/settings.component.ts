import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-settings',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  template: `
    <div class="page-container">
      <div class="page-header">
        <h1>{{ 'settings.title' | translate }}</h1>
        <div class="header-actions">
          <select class="form-select me-2" [(ngModel)]="currentLanguage" (change)="loadSettings()" style="width: auto;">
            <option value="en">English</option>
            <option value="ar">العربية</option>
          </select>
          <button class="btn btn-primary" (click)="saveSettings()" [disabled]="saving">
            <i class="bi bi-save"></i> 
            {{ saving ? 'Saving...' : ('settings.save' | translate) }}
          </button>
        </div>
      </div>

      <div class="alert alert-success" *ngIf="successMessage">
        {{ successMessage }}
      </div>

      <div class="alert alert-danger" *ngIf="errorMessage">
        {{ errorMessage }}
      </div>

      <div class="settings-section" *ngIf="!loading">
        <!-- Hero Section -->
        <h3>Hero Section</h3>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Hero Title</label>
            <input type="text" class="form-control" [(ngModel)]="settings.hero_title">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Hero Subtitle</label>
            <input type="text" class="form-control" [(ngModel)]="settings.hero_subtitle">
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Hero Description</label>
            <textarea class="form-control" rows="3" [(ngModel)]="settings.hero_description"></textarea>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">CTA Demo Button Text</label>
            <input type="text" class="form-control" [(ngModel)]="settings.hero_cta_demo" placeholder="Request Demo">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">CTA Start Button Text</label>
            <input type="text" class="form-control" [(ngModel)]="settings.hero_cta_start" placeholder="Get Started">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Pricing Hint</label>
            <input type="text" class="form-control" [(ngModel)]="settings.hero_pricing_hint" placeholder="Plans from $20/month">
          </div>
        </div>

        <!-- General Settings -->
        <h3 class="mt-4">{{ 'settings.general' | translate }}</h3>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.siteName' | translate }}</label>
            <input type="text" class="form-control" [(ngModel)]="settings.site_name">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.siteTagline' | translate }}</label>
            <input type="text" class="form-control" [(ngModel)]="settings.site_tagline">
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Company Description</label>
            <textarea class="form-control" rows="3" [(ngModel)]="settings.company_description"></textarea>
          </div>
        </div>

        <!-- Contact Information -->
        <h3 class="mt-4">{{ 'settings.contact' | translate }}</h3>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">{{ 'settings.contactEmail' | translate }}</label>
            <input type="email" class="form-control" [(ngModel)]="settings.contact_email">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">{{ 'settings.contactPhone' | translate }}</label>
            <input type="text" class="form-control" [(ngModel)]="settings.contact_phone">
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">{{ 'settings.contactAddress' | translate }}</label>
            <input type="text" class="form-control" [(ngModel)]="settings.contact_address">
          </div>
        </div>

        <!-- Social Media Links -->
        <h3 class="mt-4">{{ 'settings.social' | translate }}</h3>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.facebook' | translate }}</label>
            <input type="url" class="form-control" [(ngModel)]="settings.social_facebook" placeholder="https://facebook.com/ashub">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.twitter' | translate }}</label>
            <input type="url" class="form-control" [(ngModel)]="settings.social_twitter" placeholder="https://twitter.com/ashub">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.linkedin' | translate }}</label>
            <input type="url" class="form-control" [(ngModel)]="settings.social_linkedin" placeholder="https://linkedin.com/company/ashub">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ 'settings.instagram' | translate }}</label>
            <input type="url" class="form-control" [(ngModel)]="settings.social_instagram" placeholder="https://instagram.com/ashub">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">YouTube</label>
            <input type="url" class="form-control" [(ngModel)]="settings.social_youtube" placeholder="https://youtube.com/@ashub">
          </div>
        </div>
      </div>

      <div *ngIf="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>
    </div>
  `,
  styles: [`
    .page-container {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }
    .header-actions {
      display: flex;
      align-items: center;
    }
    .settings-section h3 {
      font-size: 18px;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 16px;
      padding-bottom: 8px;
      border-bottom: 2px solid #e5e7eb;
    }
    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
    }
    .alert-success {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #10b981;
    }
    .alert-danger {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #ef4444;
    }
  `]
})
export class SettingsComponent implements OnInit {
  settings: any = {
    // Hero Section
    hero_title: '',
    hero_subtitle: '',
    hero_description: '',
    hero_cta_demo: '',
    hero_cta_start: '',
    hero_pricing_hint: '',
    // General
    site_name: '',
    site_tagline: '',
    company_description: '',
    // Contact
    contact_email: '',
    contact_phone: '',
    contact_address: '',
    // Social
    social_facebook: '',
    social_twitter: '',
    social_linkedin: '',
    social_instagram: '',
    social_youtube: ''
  };
  loading: boolean = true;
  saving: boolean = false;
  currentLanguage: string = 'en';
  successMessage: string = '';
  errorMessage: string = '';

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadSettings();
  }

  loadSettings(): void {
    this.loading = true;
    this.successMessage = '';
    this.errorMessage = '';
    
    this.apiService.get(`/admin/settings?language=${this.currentLanguage}`).subscribe({
      next: (response: any) => {
        console.log('Settings loaded:', response);
        
        // Flatten the grouped settings
        if (response.data) {
          const flatSettings: any = {};
          Object.keys(response.data).forEach(group => {
            Object.keys(response.data[group]).forEach(key => {
              flatSettings[key] = response.data[group][key].value;
            });
          });
          this.settings = { ...this.settings, ...flatSettings };
        }
        
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading settings:', error);
        this.errorMessage = 'Failed to load settings';
        this.loading = false;
      }
    });
  }

  saveSettings(): void {
    this.saving = true;
    this.successMessage = '';
    this.errorMessage = '';
    
    const payload = {
      language: this.currentLanguage,
      settings: this.settings
    };
    
    console.log('Saving settings:', payload);
    
    this.apiService.put('/admin/settings', payload).subscribe({
      next: (response: any) => {
        console.log('Settings saved:', response);
        this.successMessage = 'Settings saved successfully!';
        this.saving = false;
        
        // Hide success message after 3 seconds
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      },
      error: (error) => {
        console.error('Error saving settings:', error);
        this.errorMessage = error.error?.message || 'Error saving settings';
        this.saving = false;
        
        // Hide error message after 5 seconds
        setTimeout(() => {
          this.errorMessage = '';
        }, 5000);
      }
    });
  }
}
