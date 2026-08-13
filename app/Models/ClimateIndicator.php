<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClimateIndicator extends Model
{
    protected $fillable = [
        'external_code',
        'name',
        'unit',
        'theme_1',
        'theme_2',
        'theme_3',
        'theme_4',
        'source',
        'description',
        'is_active',
        'is_ai_selectable',
        'comparison_direction',
        'weight',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_ai_selectable' => 'boolean',
        'weight' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ClimateValue::class);
    }
}
