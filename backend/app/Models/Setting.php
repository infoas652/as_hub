<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'language',
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Scope a query to filter by language.
     */
    public function scopeLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to filter by key.
     */
    public function scopeKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get the value with proper type casting.
     */
    public function getTypedValue()
    {
        switch ($this->type) {
            case 'json':
                return json_decode($this->value, true);
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : $this->value;
            default:
                return $this->value;
        }
    }

    /**
     * Get all settings as key-value pairs for a specific language.
     */
    public static function getAllForLanguage($language = 'en')
    {
        return static::where('language', $language)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getTypedValue()];
            });
    }

    /**
     * Get settings grouped by group for a specific language.
     */
    public static function getGroupedForLanguage($language = 'en')
    {
        return static::where('language', $language)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getTypedValue()];
            });
    }
}
