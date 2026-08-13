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
        Schema::create('climate_regions', function (Blueprint $table) {
            $table->id();
            $table->string('external_code')->unique();
            $table->string('name');
            $table->string('region_type');
            $table->string('parent_external_code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('region_type');
            $table->index('parent_external_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_regions');
    }
};
