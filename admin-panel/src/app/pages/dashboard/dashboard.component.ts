import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';

interface Stats {
  total_leads: number;
  new_leads: number;
  processed_leads: number;
  total_services: number;
  total_testimonials: number;
  total_pricing?: number;
  total_features?: number;
  total_faq?: number;
  total_media?: number;
  leads_change?: number;
}

interface Lead {
  id: number;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  plan?: string;
  status: string;
  created_at: string;
}

interface Activity {
  type: string;
  title: string;
  time: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, TranslateModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {
  stats: Stats | null = null;
  recentLeads: Lead[] = [];
  recentActivity: Activity[] = [];
  loading = true;
  adminName = 'Admin';

  constructor(
    private apiService: ApiService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.loadAdminInfo();
    this.loadDashboardData();
  }

  loadAdminInfo() {
    const user = this.authService.getUser();
    if (user) {
      this.adminName = user.name || 'Admin';
    }
  }

  loadDashboardData() {
    this.loading = true;

    // Load stats
    this.apiService.get('/admin/dashboard/stats').subscribe({
      next: (response: any) => {
        this.stats = response;
        this.generateRecentActivity();
      },
      error: (error) => {
        console.error('Error loading stats:', error);
        // Set default stats if error
        this.stats = {
          total_leads: 0,
          new_leads: 0,
          processed_leads: 0,
          total_services: 0,
          total_testimonials: 0,
          total_pricing: 0,
          total_features: 0,
          total_faq: 0,
          total_media: 0
        };
      }
    });

    // Load recent leads
    this.apiService.get('/admin/leads?per_page=5').subscribe({
      next: (response: any) => {
        // Handle paginated response
        if (response.data && response.data.data) {
          this.recentLeads = response.data.data;
        } else if (response.data) {
          this.recentLeads = Array.isArray(response.data) ? response.data : [];
        } else {
          this.recentLeads = [];
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading leads:', error);
        this.recentLeads = [];
        this.loading = false;
      }
    });
  }

  generateRecentActivity() {
    if (!this.stats) return;

    this.recentActivity = [];

    if (this.stats.new_leads > 0) {
      this.recentActivity.push({
        type: 'lead',
        title: `${this.stats.new_leads} new leads received`,
        time: 'Today'
      });
    }

    if (this.stats.total_services > 0) {
      this.recentActivity.push({
        type: 'service',
        title: `${this.stats.total_services} services active`,
        time: 'Current'
      });
    }

    if (this.stats.total_testimonials > 0) {
      this.recentActivity.push({
        type: 'testimonial',
        title: `${this.stats.total_testimonials} testimonials published`,
        time: 'This week'
      });
    }

    if (this.stats.total_pricing && this.stats.total_pricing > 0) {
      this.recentActivity.push({
        type: 'pricing',
        title: `${this.stats.total_pricing} pricing plans available`,
        time: 'Current'
      });
    }
  }

  refreshData() {
    this.loadDashboardData();
  }

  getActivityIcon(type: string): string {
    const icons: { [key: string]: string } = {
      'lead': '📧',
      'service': '🛠️',
      'testimonial': '💬',
      'pricing': '💰',
      'feature': '⭐',
      'faq': '❓',
      'media': '🖼️',
      'setting': '⚙️'
    };
    return icons[type] || '📌';
  }

  getStatusClass(status: string): string {
    const statusClasses: { [key: string]: string } = {
      'new': 'success',
      'contacted': 'info',
      'qualified': 'warning',
      'converted': 'primary',
      'closed': 'secondary'
    };
    return statusClasses[status] || 'secondary';
  }

  formatDate(dateString: string): string {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now.getTime() - date.getTime());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
      return 'Today';
    } else if (diffDays === 1) {
      return 'Yesterday';
    } else if (diffDays < 7) {
      return `${diffDays} days ago`;
    } else {
      return date.toLocaleDateString();
    }
  }

  getCurrentDate(): string {
    const now = new Date();
    return now.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }
}
