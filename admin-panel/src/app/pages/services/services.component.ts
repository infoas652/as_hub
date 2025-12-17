import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, FormArray, Validators } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

interface Service {
  id?: number;
  language: string;
  title: string;
  slug?: string;
  icon: string;
  description: string;
  features: string[];
  order: number;
  is_active: boolean;
}

@Component({
  selector: 'app-services',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './services.component.html',
  styleUrls: ['./services.component.scss']
})
export class ServicesComponent implements OnInit {
  services: Service[] = [];
  filteredServices: Service[] = [];
  loading = false;
  saving = false;
  showModal = false;
  isEditMode = false;
  selectedLanguage = 'all';
  searchTerm = '';
  
  serviceForm!: FormGroup;
  currentService: Service | null = null;
  selectedFile: File | null = null;
  imagePreview: string | null = null;
  useEmoji: boolean = true;

  constructor(
    private apiService: ApiService,
    private fb: FormBuilder
  ) {
    this.initForm();
  }

  ngOnInit() {
    this.loadServices();
  }

  initForm() {
    this.serviceForm = this.fb.group({
      language: ['en', Validators.required],
      title: ['', Validators.required],
      icon: ['🛠️'],
      description: [''],
      features: this.fb.array([]),
      order: [0],
      is_active: [true]
    });

    // Add initial feature field
    this.addFeature();
  }

  get features(): FormArray {
    return this.serviceForm.get('features') as FormArray;
  }

  addFeature() {
    this.features.push(this.fb.control(''));
  }

  removeFeature(index: number) {
    if (this.features.length > 1) {
      this.features.removeAt(index);
    }
  }

  loadServices() {
    this.loading = true;
    this.apiService.get('/admin/services').subscribe({
      next: (response: any) => {
        this.services = response.data || response || [];
        this.applyFilters();
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading services:', error);
        this.services = [];
        this.filteredServices = [];
        this.loading = false;
      }
    });
  }

  refreshData() {
    this.loadServices();
  }

  filterByLanguage(language: string) {
    this.selectedLanguage = language;
    this.applyFilters();
  }

  onSearch() {
    this.applyFilters();
  }

  applyFilters() {
    let filtered = [...this.services];

    // Language filter
    if (this.selectedLanguage !== 'all') {
      filtered = filtered.filter(service => service.language === this.selectedLanguage);
    }

    // Search filter
    if (this.searchTerm.trim()) {
      const term = this.searchTerm.toLowerCase();
      filtered = filtered.filter(service => 
        service.title.toLowerCase().includes(term) ||
        service.description?.toLowerCase().includes(term)
      );
    }

    // Sort by order
    filtered.sort((a, b) => (a.order || 0) - (b.order || 0));

    this.filteredServices = filtered;
  }

  openAddModal() {
    this.isEditMode = false;
    this.currentService = null;
    this.resetForm();
    this.showModal = true;
  }

  editService(service: Service) {
    this.isEditMode = true;
    this.currentService = service;
    this.populateForm(service);
    this.showModal = true;
  }

  resetForm() {
    this.serviceForm.reset({
      language: 'en',
      title: '',
      icon: '🛠️',
      description: '',
      order: 0,
      is_active: true
    });

    // Clear features array and add one empty field
    while (this.features.length > 0) {
      this.features.removeAt(0);
    }
    this.addFeature();
  }

  populateForm(service: Service) {
    // Check if icon is an image or emoji
    const isImage = service.icon && (service.icon.startsWith('/uploads/') || service.icon.startsWith('http'));
    
    this.serviceForm.patchValue({
      language: service.language,
      title: service.title,
      icon: isImage ? '🛠️' : service.icon,
      description: service.description,
      order: service.order,
      is_active: service.is_active
    });

    // Set icon type and preview
    if (isImage) {
      this.useEmoji = false;
      this.imagePreview = this.getIconUrl(service.icon);
      this.selectedFile = null; // We don't have the actual file, just the URL
    } else {
      this.useEmoji = true;
      this.imagePreview = null;
      this.selectedFile = null;
    }

    // Populate features
    while (this.features.length > 0) {
      this.features.removeAt(0);
    }
    
    if (service.features && service.features.length > 0) {
      service.features.forEach(feature => {
        this.features.push(this.fb.control(feature));
      });
    } else {
      this.addFeature();
    }
  }

  onFileSelected(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.selectedFile = file;
      this.useEmoji = false;
      
      // Create image preview
      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.imagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  removeImage() {
    this.selectedFile = null;
    this.imagePreview = null;
    this.useEmoji = true;
  }

  toggleIconType() {
    this.useEmoji = !this.useEmoji;
    if (this.useEmoji) {
      this.selectedFile = null;
      this.imagePreview = null;
    }
  }

  saveService() {
    if (this.serviceForm.invalid) {
      Object.keys(this.serviceForm.controls).forEach(key => {
        this.serviceForm.get(key)?.markAsTouched();
      });
      return;
    }

    this.saving = true;
    const formValue = this.serviceForm.value;
    
    // Filter out empty features
    formValue.features = formValue.features.filter((f: string) => f.trim() !== '');

    // Create FormData for file upload
    const formData = new FormData();
    formData.append('language', formValue.language);
    formData.append('title', formValue.title);
    formData.append('description', formValue.description || '');
    formData.append('order', formValue.order.toString());
    formData.append('is_active', formValue.is_active ? '1' : '0');
    
    // Add features as JSON string
    formData.append('features', JSON.stringify(formValue.features));
    
    // Add icon (emoji or file)
    if (this.selectedFile) {
      formData.append('icon_file', this.selectedFile);
    } else {
      formData.append('icon', formValue.icon || '🛠️');
    }

    // For edit mode, add _method field for Laravel
    if (this.isEditMode && this.currentService) {
      formData.append('_method', 'PUT');
    }

    const request = this.isEditMode && this.currentService
      ? this.apiService.post(`/admin/services/${this.currentService.id}`, formData)
      : this.apiService.post('/admin/services', formData);

    request.subscribe({
      next: (response: any) => {
        this.saving = false;
        this.closeModal();
        this.loadServices();
        this.showSuccessMessage(this.isEditMode ? 'Service updated successfully!' : 'Service created successfully!');
      },
      error: (error) => {
        console.error('Error saving service:', error);
        this.saving = false;
        this.showErrorMessage('Error saving service. Please try again.');
      }
    });
  }

  toggleStatus(service: Service) {
    if (!service.id) return;

    this.apiService.post(`/admin/services/${service.id}/toggle`, {}).subscribe({
      next: (response: any) => {
        service.is_active = !service.is_active;
        this.showSuccessMessage('Status updated successfully!');
      },
      error: (error) => {
        console.error('Error updating status:', error);
        this.showErrorMessage('Error updating status. Please try again.');
      }
    });
  }

  showSuccessMessage(message: string) {
    // You can replace this with a toast notification library
    alert(message);
  }

  showErrorMessage(message: string) {
    // You can replace this with a toast notification library
    alert(message);
  }

  deleteService(service: Service) {
    if (!service.id) return;

    if (!confirm('Are you sure you want to delete this service?')) {
      return;
    }

    this.apiService.delete(`/admin/services/${service.id}`).subscribe({
      next: () => {
        this.loadServices();
        alert('Service deleted successfully!');
      },
      error: (error) => {
        console.error('Error deleting service:', error);
        alert('Error deleting service. Please try again.');
      }
    });
  }

  closeModal() {
    this.showModal = false;
    this.resetForm();
    this.selectedFile = null;
    this.imagePreview = null;
    this.useEmoji = true;
  }

  getIconDisplay(service: Service): string {
    if (service.icon && (service.icon.startsWith('/uploads/') || service.icon.startsWith('http'))) {
      return 'image';
    }
    return 'emoji';
  }

  getIconUrl(icon: string): string {
    if (icon.startsWith('http')) {
      return icon;
    }
    return `http://localhost:8000${icon}`;
  }
}
