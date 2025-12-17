<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'plan',
        'source',
        'status',
        'is_processed',
        'processed_at',
        'processed_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin who processed this lead.
     */
    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    /**
     * Scope a query to only include new leads.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope a query to only include processed leads.
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope a query to only include unprocessed leads.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by plan.
     */
    public function scopePlan($query, $plan)
    {
        return $query->where('plan', $plan);
    }

    /**
     * Mark the lead as processed.
     */
    public function markAsProcessed($adminId = null)
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
            'processed_by' => $adminId,
        ]);
    }
}
