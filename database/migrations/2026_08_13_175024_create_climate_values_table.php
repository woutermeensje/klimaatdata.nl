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
        Schema::create('climate_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('climate_indicator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('climate_region_id')->constrained()->cascadeOnDelete();
            $table->string('period');
            $table->string('period_type')->nullable();
            $table->decimal('value', 20, 6)->nullable();
            $table->string('raw_value')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['climate_indicator_id', 'climate_region_id', 'period'], 'climate_values_unique_measurement');
            $table->index('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_values');
    }
};
