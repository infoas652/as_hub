import { Component, OnInit, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule } from '@ngx-translate/core';
import { TranslationService } from '../../services/translation.service';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent implements OnInit {
  isScrolled = false;
  isMobileMenuOpen = false;
  currentLanguage = 'en';

  menuItems = [
    { label: 'header.home', link: '#home' },
    { label: 'header.services', link: '#services' },
    { label: 'header.pricing', link: '#pricing' },
    { label: 'header.features', link: '#features' },
    { label: 'header.testimonials', link: '#testimonials' },
    { label: 'header.faq', link: '#faq' },
    { label: 'header.contact', link: '#contact' }
  ];

  constructor(public translationService: TranslationService) {}

  ngOnInit(): void {
    this.translationService.currentLanguage$.subscribe(lang => {
      this.currentLanguage = lang;
    });
  }

  @HostListener('window:scroll', [])
  onWindowScroll(): void {
    this.isScrolled = window.pageYOffset > 50;
  }

  toggleMobileMenu(): void {
    this.isMobileMenuOpen = !this.isMobileMenuOpen;
  }

  toggleLanguage(): void {
    this.translationService.toggleLanguage();
  }

  scrollToSection(link: string): void {
    const element = document.querySelector(link);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      this.isMobileMenuOpen = false;
    }
  }
}
