<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClimateValue extends Model
{
    protected $fillable = [
        'climate_indicator_id',
        'climate_region_id',
        'period',
        'period_type',
        'value',
        'raw_value',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:6',
        'metadata' => 'array',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(ClimateIndicator::class, 'climate_indicator_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(ClimateRegion::class, 'climate_region_id');
    }
}
