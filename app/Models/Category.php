<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'parent_id',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public $translatable = ['name', 'description'];

    // Relationships
    public function artifacts(): HasMany
    {
        return $this->hasMany(Artifact::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getNameAttribute($value)
    {
        $names = json_decode($value, true);
        return $names[app()->getLocale()] ?? $names['en'] ?? '';
    }

    public function getDescriptionAttribute($value)
    {
        if (!$value) return '';
        $descriptions = json_decode($value, true);
        return $descriptions[app()->getLocale()] ?? $descriptions['en'] ?? '';
    }
}
