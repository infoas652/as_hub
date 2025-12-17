import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-testimonials',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  template: `
    <div class="page-container">
      <div class="page-header">
        <h1>{{ 'testimonials.title' | translate }}</h1>
        <button class="btn btn-primary" (click)="openAddModal()">
          <i class="bi bi-plus-lg"></i> {{ 'testimonials.add' | translate }}
        </button>
      </div>
      <div class="table-responsive" *ngIf="!loading && testimonials.length > 0">
        <table class="table">
          <thead>
            <tr>
              <th>{{ 'testimonials.clientName' | translate }}</th>
              <th>{{ 'testimonials.clientCompany' | translate }}</th>
              <th>{{ 'testimonials.rating' | translate }}</th>
              <th>{{ 'common.actions' | translate }}</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let testimonial of testimonials">
              <td>{{ testimonial.client_name }}</td>
              <td>{{ testimonial.client_company }}</td>
              <td>{{ testimonial.rating }}/5</td>
              <td>
                <button class="btn btn-sm btn-outline-primary me-2" (click)="openEditModal(testimonial)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" (click)="deleteTestimonial(testimonial.id)">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
  `]
})
export class TestimonialsComponent implements OnInit {
  testimonials: any[] = [];
  loading: boolean = true;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadTestimonials();
  }

  loadTestimonials(): void {
    this.loading = true;
    this.apiService.getTestimonials().subscribe({
      next: (data) => {
        this.testimonials = data.data || [];
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  openAddModal(): void {
    alert('Add testimonial modal');
  }

  openEditModal(testimonial: any): void {
    alert('Edit testimonial: ' + testimonial.client_name);
  }

  deleteTestimonial(id: number): void {
    if (confirm('Are you sure?')) {
      this.apiService.deleteTestimonial(id).subscribe({
        next: () => this.loadTestimonials()
      });
    }
  }
}
