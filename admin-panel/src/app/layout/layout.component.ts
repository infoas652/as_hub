import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss']
})
export class LayoutComponent implements OnInit {
  isSidebarOpen = true;
  currentLang = 'en';
  adminName = 'Admin';
  
  menuItems = [
    { 
      icon: '📊', 
      label: 'dashboard', 
      route: '/dashboard',
      badge: null
    },
    { 
      icon: '🛠️', 
      label: 'services', 
      route: '/services',
      badge: null
    },
    { 
      icon: '💰', 
      label: 'pricing', 
      route: '/pricing',
      badge: null
    },
    { 
      icon: '⭐', 
      label: 'features', 
      route: '/features',
      badge: null
    },
    { 
      icon: '💬', 
      label: 'testimonials', 
      route: '/testimonials',
      badge: null
    },
    { 
      icon: '❓', 
      label: 'faq', 
      route: '/faq',
      badge: null
    },
    { 
      icon: '📧', 
      label: 'leads', 
      route: '/leads',
      badge: 'new'
    },
    { 
      icon: '🖼️', 
      label: 'media', 
      route: '/media',
      badge: null
    },
    { 
      icon: '⚙️', 
      label: 'settings', 
      route: '/settings',
      badge: null
    },
    { 
      icon: '👤', 
      label: 'profile', 
      route: '/profile',
      badge: null
    }
  ];

  constructor(
    public translate: TranslateService,
    private authService: AuthService,
    private router: Router
  ) {
    // Get saved language or default to English
    this.currentLang = localStorage.getItem('adminLang') || 'en';
    this.translate.use(this.currentLang);
    this.updateDirection();
  }

  ngOnInit() {
    // Get admin info from token or storage
    const user = this.authService.getUser();
    if (user) {
      this.adminName = user.name || 'Admin';
    }
  }

  toggleSidebar() {
    this.isSidebarOpen = !this.isSidebarOpen;
  }

  switchLanguage(lang: string) {
    this.currentLang = lang;
    this.translate.use(lang);
    localStorage.setItem('adminLang', lang);
    this.updateDirection();
  }

  updateDirection() {
    const dir = this.currentLang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.dir = dir;
    document.documentElement.lang = this.currentLang;
  }

  logout() {
    if (confirm(this.translate.instant('confirmLogout'))) {
      this.authService.logout();
      this.router.navigate(['/login']);
    }
  }

  isActive(route: string): boolean {
    return this.router.url === route;
  }
}
