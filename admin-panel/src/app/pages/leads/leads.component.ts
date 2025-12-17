import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

interface Lead {
  id: number;
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  plan?: string;
  status: string;
  is_processed: boolean;
  processed_at?: string;
  created_at: string;
}

@Component({
  selector: 'app-leads',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  templateUrl: './leads.component.html',
  styleUrls: ['./leads.component.scss']
})
export class LeadsComponent implements OnInit {
  leads: Lead[] = [];
  selectedLead: Lead | null = null;
  showModal = false;
  loading = true;
  
  // Filters
  selectedStatus = 'all';
  selectedProcessed = 'all';
  searchTerm = '';
  
  constructor(private apiService: ApiService) {}

  ngOnInit() {
    this.loadLeads();
  }

  loadLeads() {
    this.loading = true;
    
    let url = '/admin/leads?per_page=100';
    
    if (this.selectedStatus !== 'all') {
      url += `&status=${this.selectedStatus}`;
    }
    
    if (this.selectedProcessed !== 'all') {
      url += `&processed=${this.selectedProcessed}`;
    }
    
    if (this.searchTerm) {
      url += `&search=${encodeURIComponent(this.searchTerm)}`;
    }

    this.apiService.get(url).subscribe({
      next: (response: any) => {
        if (response.success && response.data) {
          // Handle paginated response
          if (response.data.data) {
            this.leads = response.data.data;
          } else if (Array.isArray(response.data)) {
            this.leads = response.data;
          } else {
            this.leads = [];
          }
        } else {
          this.leads = [];
        }
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading leads:', error);
        this.leads = [];
        this.loading = false;
      }
    });
  }

  applyFilters() {
    this.loadLeads();
  }

  onSearch() {
    // Debounce search
    setTimeout(() => {
      this.loadLeads();
    }, 300);
  }

  refreshData() {
    this.loadLeads();
  }

  viewLead(lead: Lead) {
    this.selectedLead = lead;
    this.showModal = true;
  }

  closeModal() {
    this.showModal = false;
    this.selectedLead = null;
  }

  markAsProcessed(lead: Lead) {
    if (confirm('Mark this lead as processed?')) {
      this.apiService.post(`/admin/leads/${lead.id}/process`, {}).subscribe({
        next: (response: any) => {
          if (response.success) {
            lead.is_processed = true;
            lead.processed_at = new Date().toISOString();
            this.closeModal();
            this.loadLeads();
          }
        },
        error: (error) => {
          console.error('Error marking lead as processed:', error);
          alert('Failed to mark lead as processed');
        }
      });
    }
  }

  deleteLead(lead: Lead) {
    if (confirm(`Delete lead from ${lead.name}?`)) {
      this.apiService.delete(`/admin/leads/${lead.id}`).subscribe({
        next: (response: any) => {
          if (response.success) {
            this.leads = this.leads.filter(l => l.id !== lead.id);
            this.closeModal();
          }
        },
        error: (error) => {
          console.error('Error deleting lead:', error);
          alert('Failed to delete lead');
        }
      });
    }
  }

  exportLeads() {
    let url = '/admin/leads/export';
    
    if (this.selectedStatus !== 'all') {
      url += `?status=${this.selectedStatus}`;
    }
    
    if (this.selectedProcessed !== 'all') {
      const separator = url.includes('?') ? '&' : '?';
      url += `${separator}processed=${this.selectedProcessed}`;
    }

    // Open export URL in new window
    window.open(`${this.apiService['apiUrl']}${url}`, '_blank');
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
}
