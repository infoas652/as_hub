import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TranslateModule } from '@ngx-translate/core';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';

interface Admin {
  id: number;
  name: string;
  email: string;
  avatar?: string;
}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, FormsModule, TranslateModule],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.scss'
})
export class ProfileComponent implements OnInit {
  admin: Admin | null = null;
  loading = false;
  saving = false;
  
  // Profile Form
  name = '';
  email = '';
  avatar = '';
  
  // Password Form
  currentPassword = '';
  newPassword = '';
  confirmPassword = '';
  
  // Messages
  successMessage = '';
  errorMessage = '';
  passwordSuccessMessage = '';
  passwordErrorMessage = '';
  
  // Show/Hide Password
  showCurrentPassword = false;
  showNewPassword = false;
  showConfirmPassword = false;

  constructor(
    private apiService: ApiService,
    private authService: AuthService
  ) {}

  ngOnInit() {
    this.loadProfile();
  }

  loadProfile() {
    this.loading = true;
    this.apiService.get('/admin/profile').subscribe({
      next: (response: any) => {
        const data = response.data || response;
        this.admin = data;
        this.name = data.name || '';
        this.email = data.email || '';
        this.avatar = data.avatar || '';
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.errorMessage = 'Failed to load profile';
        this.loading = false;
      }
    });
  }

  updateProfile() {
    if (!this.name || !this.email) {
      this.errorMessage = 'Name and email are required';
      return;
    }

    this.saving = true;
    this.errorMessage = '';
    this.successMessage = '';

    const data = {
      name: this.name,
      email: this.email,
      avatar: this.avatar
    };

    this.apiService.put('/admin/profile', data).subscribe({
      next: (response: any) => {
        const updatedData = response.data || response;
        this.admin = updatedData;
        this.name = updatedData.name;
        this.email = updatedData.email;
        this.avatar = updatedData.avatar || '';
        this.successMessage = 'Profile updated successfully!';
        this.saving = false;
        
        // Clear success message after 3 seconds
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      },
      error: (error) => {
        console.error('Error updating profile:', error);
        this.errorMessage = error.error?.message || 'Failed to update profile';
        this.saving = false;
      }
    });
  }

  updatePassword() {
    // Validation
    if (!this.currentPassword || !this.newPassword || !this.confirmPassword) {
      this.passwordErrorMessage = 'All password fields are required';
      return;
    }

    if (this.newPassword.length < 8) {
      this.passwordErrorMessage = 'New password must be at least 8 characters';
      return;
    }

    if (this.newPassword !== this.confirmPassword) {
      this.passwordErrorMessage = 'New passwords do not match';
      return;
    }

    this.saving = true;
    this.passwordErrorMessage = '';
    this.passwordSuccessMessage = '';

    const data = {
      current_password: this.currentPassword,
      new_password: this.newPassword,
      new_password_confirmation: this.confirmPassword
    };

    this.apiService.put('/admin/password', data).subscribe({
      next: (response: any) => {
        this.passwordSuccessMessage = 'Password updated successfully!';
        this.currentPassword = '';
        this.newPassword = '';
        this.confirmPassword = '';
        this.saving = false;
        
        // Clear success message after 3 seconds
        setTimeout(() => {
          this.passwordSuccessMessage = '';
        }, 3000);
      },
      error: (error) => {
        console.error('Error updating password:', error);
        this.passwordErrorMessage = error.error?.message || 'Failed to update password';
        this.saving = false;
      }
    });
  }

  togglePasswordVisibility(field: string) {
    switch(field) {
      case 'current':
        this.showCurrentPassword = !this.showCurrentPassword;
        break;
      case 'new':
        this.showNewPassword = !this.showNewPassword;
        break;
      case 'confirm':
        this.showConfirmPassword = !this.showConfirmPassword;
        break;
    }
  }

  getInitials(): string {
    if (!this.name) return 'A';
    const names = this.name.split(' ');
    if (names.length >= 2) {
      return names[0].charAt(0) + names[1].charAt(0);
    }
    return this.name.charAt(0);
  }
}
