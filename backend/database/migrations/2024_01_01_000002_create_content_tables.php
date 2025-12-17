<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Services Table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->string('title');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('language');
            $table->index('slug');
            $table->index('is_active');
            $table->index('order');
        });

        // Pricing Plans Table
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('language');
            $table->index('slug');
            $table->index('is_active');
            $table->index('order');
        });

        // Features Table
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('language');
            $table->index('is_active');
            $table->index('order');
        });

        // Testimonials Table
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->string('client_name');
            $table->string('client_position')->nullable();
            $table->string('client_company')->nullable();
            $table->string('client_avatar')->nullable();
            $table->text('testimonial');
            $table->tinyInteger('rating')->unsigned()->default(5);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('language');
            $table->index('is_active');
            $table->index('order');
        });

        // FAQ Table
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->text('question');
            $table->text('answer');
            $table->string('category', 100)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('language');
            $table->index('category');
            $table->index('is_active');
            $table->index('order');
        });

        // Leads Table
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('company')->nullable();
            $table->text('message');
            $table->string('plan', 100)->nullable();
            $table->string('source', 100)->default('website');
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'closed'])->default('new');
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('is_processed');
            $table->index('created_at');
        });

        // Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->enum('language', ['en', 'ar'])->default('en');
            $table->string('key');
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'textarea', 'json', 'boolean', 'number'])->default('text');
            $table->string('group', 100)->default('general');
            $table->timestamps();

            $table->unique(['language', 'key']);
            $table->index('group');
        });

        // Media Table
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->bigInteger('size')->unsigned();
            $table->string('path', 500);
            $table->string('url', 500);
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            $table->index('mime_type');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('faq');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('features');
        Schema::dropIfExists('pricing_plans');
        Schema::dropIfExists('services');
    }
};
