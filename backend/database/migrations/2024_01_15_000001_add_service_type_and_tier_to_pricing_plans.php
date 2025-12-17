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
        Schema::table('pricing_plans', function (Blueprint $table) {
            // Add service_type column after language
            $table->enum('service_type', ['website', 'app', 'both'])
                  ->default('website')
                  ->after('language');
            
            // Add tier column after service_type
            $table->enum('tier', ['basic', 'professional', 'enterprise'])
                  ->default('basic')
                  ->after('service_type');
            
            // Add indexes for better query performance
            $table->index('service_type');
            $table->index('tier');
            $table->index(['service_type', 'tier', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->dropIndex(['pricing_plans_service_type_index']);
            $table->dropIndex(['pricing_plans_tier_index']);
            $table->dropIndex(['pricing_plans_service_type_tier_language_index']);
            $table->dropColumn(['service_type', 'tier']);
        });
    }
};
