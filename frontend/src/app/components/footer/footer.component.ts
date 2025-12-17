import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.scss']
})
export class FooterComponent implements OnInit, OnDestroy {
  currentYear = new Date().getFullYear();
  currentLanguage: string = 'en';
  loading = true;
  
  // Contact Info from Settings
  contactEmail = '';
  contactPhone = '';
  contactAddress = '';
  companyDescription = '';
  
  // Social Links from Settings
  socialLinks: any[] = [];
  
  // Static navigation links
  quickLinks = [
    { label: 'Home', href: '#home' },
    { label: 'Services', href: '#services' },
    { label: 'Pricing', href: '#pricing' },
    { label: 'Contact', href: '#contact' }
  ];

  services = [
    { label: 'Web Development', href: '#services' },
    { label: 'Mobile Development', href: '#services' },
    { label: 'E-commerce', href: '#services' },
    { label: 'Management Systems', href: '#services' }
  ];

  support = [
    { label: 'FAQ', href: '#faq' },
    { label: 'Contact', href: '#contact' },
    { label: 'Support', href: '#contact' }
  ];

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
        this.loadFooterContent();
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadFooterContent(): void {
    this.loading = true;
    console.log('🔄 Loading footer content for language:', this.currentLanguage);
    
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Footer content API response:', response);
        
        // Get settings from response
        let settings: any = {};
        if (response.data) {
          settings = response.data.settings || {};
        } else {
          settings = response.settings || {};
        }
        
        // Load contact info from settings
        this.contactEmail = settings.contact_email || '';
        this.contactPhone = settings.contact_phone || '';
        this.contactAddress = settings.contact_address || '';
        this.companyDescription = settings.company_description || settings.site_tagline || '';
        
        // Load social links from settings
        this.socialLinks = [];
        if (settings.social_facebook) {
          this.socialLinks.push({ icon: 'bi-facebook', url: settings.social_facebook, label: 'Facebook' });
        }
        if (settings.social_twitter) {
          this.socialLinks.push({ icon: 'bi-twitter', url: settings.social_twitter, label: 'Twitter' });
        }
        if (settings.social_linkedin) {
          this.socialLinks.push({ icon: 'bi-linkedin', url: settings.social_linkedin, label: 'LinkedIn' });
        }
        if (settings.social_instagram) {
          this.socialLinks.push({ icon: 'bi-instagram', url: settings.social_instagram, label: 'Instagram' });
        }
        if (settings.social_youtube) {
          this.socialLinks.push({ icon: 'bi-youtube', url: settings.social_youtube, label: 'YouTube' });
        }
        
        this.loading = false;
        console.log('📊 Footer content loaded');
      },
      error: (error) => {
        console.error('❌ Error loading footer content:', error);
        this.loading = false;
        // No fallback - show empty
        this.contactEmail = '';
        this.contactPhone = '';
        this.contactAddress = '';
        this.companyDescription = '';
        this.socialLinks = [];
      }
    });
  }

  scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}
