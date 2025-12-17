import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-faq',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  template: `
    <div class="page-container">
      <div class="page-header">
        <h1>{{ 'faq.title' | translate }}</h1>
        <button class="btn btn-primary" (click)="openAddModal()">
          <i class="bi bi-plus-lg"></i> {{ 'faq.add' | translate }}
        </button>
      </div>
      <div class="table-responsive" *ngIf="!loading && faqs.length > 0">
        <table class="table">
          <thead>
            <tr>
              <th>{{ 'faq.question' | translate }}</th>
              <th>{{ 'faq.answer' | translate }}</th>
              <th>{{ 'common.actions' | translate }}</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let faq of faqs">
              <td>{{ faq.question }}</td>
              <td>{{ faq.answer }}</td>
              <td>
                <button class="btn btn-sm btn-outline-primary me-2" (click)="openEditModal(faq)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" (click)="deleteFaq(faq.id)">
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
export class FaqComponent implements OnInit {
  faqs: any[] = [];
  loading: boolean = true;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadFaqs();
  }

  loadFaqs(): void {
    this.loading = true;
    this.apiService.getFaq().subscribe({
      next: (data) => {
        this.faqs = data.data || [];
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  openAddModal(): void {
    alert('Add FAQ modal');
  }

  openEditModal(faq: any): void {
    alert('Edit FAQ: ' + faq.question);
  }

  deleteFaq(id: number): void {
    if (confirm('Are you sure?')) {
      this.apiService.deleteFaq(id).subscribe({
        next: () => this.loadFaqs()
      });
    }
  }
}
