import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, TranslateModule],
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.scss']
})
export class ContactComponent {
  contactForm: FormGroup;
  submitted = false;
  loading = false;
  success = false;
  error = false;
  errorMessage = '';

  constructor(
    private fb: FormBuilder,
    private apiService: ApiService
  ) {
    this.contactForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(2)]],
      email: ['', [Validators.required, Validators.email]],
      phone: ['', [Validators.pattern(/^[+]?[\d\s-()]+$/)]],
      company: [''],
      plan: [''],
      message: ['', [Validators.required, Validators.minLength(10)]]
    });
  }

  get f() {
    return this.contactForm.controls;
  }

  onSubmit(): void {
    this.submitted = true;
    this.success = false;
    this.error = false;

    if (this.contactForm.invalid) {
      return;
    }

    this.loading = true;

    this.apiService.submitLead(this.contactForm.value).subscribe({
      next: (response) => {
        this.loading = false;
        this.success = true;
        this.contactForm.reset();
        this.submitted = false;
        
        // Hide success message after 5 seconds
        setTimeout(() => {
          this.success = false;
        }, 5000);
      },
      error: (error) => {
        this.loading = false;
        this.error = true;
        this.errorMessage = error.error?.message || 'contact.form.errorGeneric';
        
        // Hide error message after 5 seconds
        setTimeout(() => {
          this.error = false;
        }, 5000);
      }
    });
  }

  resetForm(): void {
    this.contactForm.reset();
    this.submitted = false;
    this.success = false;
    this.error = false;
  }
}
