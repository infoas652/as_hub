import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { TranslationService } from './services/translation.service';

// Import all components
import { HeaderComponent } from './components/header/header.component';
import { HeroComponent } from './components/hero/hero.component';
import { ServicesComponent } from './components/services/services.component';
import { FeaturesComponent } from './components/features/features.component';
import { PricingComponent } from './components/pricing/pricing.component';
import { TestimonialsComponent } from './components/testimonials/testimonials.component';
import { FaqComponent } from './components/faq/faq.component';
import { ContactComponent } from './components/contact/contact.component';
import { FooterComponent } from './components/footer/footer.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    RouterOutlet,
    HeaderComponent,
    HeroComponent,
    ServicesComponent,
    FeaturesComponent,
    PricingComponent,
    TestimonialsComponent,
    FaqComponent,
    ContactComponent,
    FooterComponent
  ],
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {
  title = 'AS Hub';
  currentDirection: 'ltr' | 'rtl' = 'ltr';

  constructor(
    private translate: TranslateService,
    private translationService: TranslationService
  ) {
    // Set default language
    this.translate.setDefaultLang('en');
    this.translate.use('en');
  }

  ngOnInit(): void {
    // Subscribe to language changes
    this.translationService.currentLanguage$.subscribe(lang => {
      this.currentDirection = lang === 'ar' ? 'rtl' : 'ltr';
      document.documentElement.setAttribute('dir', this.currentDirection);
      document.documentElement.setAttribute('lang', lang);
    });
  }
}
