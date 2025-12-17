<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pricing_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'language',
        'service_type',
        'tier',
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'features',
        'is_popular',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope a query to only include active pricing plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by language.
     */
    public function scopeLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope a query to filter by service type.
     */
    public function scopeServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope a query to filter by tier.
     */
    public function scopeTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    /**
     * Scope a query to order by custom order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include popular plans.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Get the savings amount (yearly vs monthly * 12).
     */
    public function getSavingsAttribute()
    {
        $monthlyTotal = $this->price_monthly * 12;
        $savings = $monthlyTotal - $this->price_yearly;
        return max(0, round($savings, 2));
    }

    /**
     * Get the savings percentage.
     */
    public function getSavingsPercentageAttribute()
    {
        $monthlyTotal = $this->price_monthly * 12;
        if ($monthlyTotal == 0) return 0;
        
        $savings = $monthlyTotal - $this->price_yearly;
        return round(($savings / $monthlyTotal) * 100);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = static::generateSlug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = static::generateSlug($plan->name);
            }
        });
    }

    /**
     * Generate a unique slug from the name.
     */
    protected static function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        
        // Ensure uniqueness
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }
}
