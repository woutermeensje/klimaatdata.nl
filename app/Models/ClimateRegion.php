<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClimateRegion extends Model
{
    protected $fillable = [
        'external_code',
        'name',
        'region_type',
        'parent_external_code',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(ClimateValue::class);
    }
}
