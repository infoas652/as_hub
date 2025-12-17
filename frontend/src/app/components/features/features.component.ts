import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { TranslationService } from '../../services/translation.service';

interface Feature {
  id: number;
  title: string;
  description: string;
  icon: string;
}

@Component({
  selector: 'app-features',
  standalone: true,
  imports: [CommonModule, TranslateModule],
  templateUrl: './features.component.html',
  styleUrls: ['./features.component.scss']
})
export class FeaturesComponent implements OnInit {
  features: Feature[] = [];
  loading = true;
  currentLanguage: string = 'en';

  constructor(
    private apiService: ApiService,
    private translationService: TranslationService
  ) {}

  ngOnInit(): void {
    // Subscribe to language changes
    this.translationService.currentLanguage$.subscribe(lang => {
      this.currentLanguage = lang;
      this.loadFeatures();
    });
  }

  loadFeatures(): void {
    this.loading = true;
    this.apiService.getContent(this.currentLanguage).subscribe({
      next: (response) => {
        console.log('✅ Features loaded from API:', response);
        
        // Handle different response formats
        if (response.data) {
          this.features = response.data.features || [];
        } else {
          this.features = response.features || [];
        }
        
        this.loading = false;
        console.log('📊 Total features:', this.features.length);
      },
      error: (error) => {
        console.error('❌ Error loading features:', error);
        this.loading = false;
        // No fallback - show empty state
        this.features = [];
      }
    });
  }
}
