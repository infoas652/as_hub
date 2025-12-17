import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-media',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  template: `
    <div class="page-container">
      <div class="page-header">
        <h1>{{ 'media.title' | translate }}</h1>
        <button class="btn btn-primary" (click)="fileInput.click()">
          <i class="bi bi-upload"></i> {{ 'media.upload' | translate }}
        </button>
        <input #fileInput type="file" hidden (change)="onFileSelected($event)" accept="image/*">
      </div>

      <div class="media-grid" *ngIf="!loading && mediaFiles.length > 0">
        <div class="media-item" *ngFor="let media of mediaFiles">
          <img [src]="media.url" [alt]="media.alt_text || media.filename">
          <div class="media-info">
            <p class="media-name">{{ media.filename }}</p>
            <p class="media-size">{{ formatFileSize(media.size) }}</p>
            <button class="btn btn-sm btn-danger" (click)="deleteMedia(media.id)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div *ngIf="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
      </div>

      <div *ngIf="!loading && mediaFiles.length === 0" class="text-center py-5">
        <p class="text-muted">{{ 'common.noData' | translate }}</p>
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
    .media-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }
    .media-item {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .media-item:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .media-item img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .media-info {
      padding: 12px;
    }
    .media-name {
      font-size: 14px;
      font-weight: 500;
      margin: 0 0 4px 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .media-size {
      font-size: 12px;
      color: #6b7280;
      margin: 0 0 8px 0;
    }
  `]
})
export class MediaComponent implements OnInit {
  mediaFiles: any[] = [];
  loading: boolean = true;

  constructor(private apiService: ApiService) {}

  ngOnInit(): void {
    this.loadMedia();
  }

  loadMedia(): void {
    this.loading = true;
    this.apiService.getMedia().subscribe({
      next: (data) => {
        this.mediaFiles = data.data || [];
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.apiService.uploadMedia(file).subscribe({
        next: () => {
          this.loadMedia();
          alert('File uploaded successfully!');
        },
        error: (err) => {
          alert('Error uploading file');
          console.error(err);
        }
      });
    }
  }

  deleteMedia(id: number): void {
    if (confirm('Are you sure?')) {
      this.apiService.deleteMedia(id).subscribe({
        next: () => this.loadMedia()
      });
    }
  }

  formatFileSize(bytes: number): string {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }
}
