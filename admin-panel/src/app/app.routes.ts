import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';
import { LayoutComponent } from './layout/layout.component';

export const routes: Routes = [
  {
    path: 'login',
    loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: '',
    component: LayoutComponent,
    canActivate: [authGuard],
    children: [
      {
        path: 'dashboard',
        loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent)
      },
      {
        path: 'services',
        loadComponent: () => import('./pages/services/services.component').then(m => m.ServicesComponent)
      },
      {
        path: 'pricing',
        loadComponent: () => import('./pages/pricing/pricing.component').then(m => m.PricingComponent)
      },
      {
        path: 'features',
        loadComponent: () => import('./pages/features/features.component').then(m => m.FeaturesComponent)
      },
      {
        path: 'testimonials',
        loadComponent: () => import('./pages/testimonials/testimonials.component').then(m => m.TestimonialsComponent)
      },
      {
        path: 'faq',
        loadComponent: () => import('./pages/faq/faq.component').then(m => m.FaqComponent)
      },
      {
        path: 'leads',
        loadComponent: () => import('./pages/leads/leads.component').then(m => m.LeadsComponent)
      },
      {
        path: 'settings',
        loadComponent: () => import('./pages/settings/settings.component').then(m => m.SettingsComponent)
      },
      {
        path: 'media',
        loadComponent: () => import('./pages/media/media.component').then(m => m.MediaComponent)
      },
      {
        path: 'profile',
        loadComponent: () => import('./pages/profile/profile.component').then(m => m.ProfileComponent)
      },
      {
        path: '',
        redirectTo: 'dashboard',
        pathMatch: 'full'
      }
    ]
  },
  {
    path: '**',
    redirectTo: '/dashboard'
  }
];
