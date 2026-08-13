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
        Schema::create('climate_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('external_code')->unique();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->string('theme_1')->nullable();
            $table->string('theme_2')->nullable();
            $table->string('theme_3')->nullable();
            $table->string('theme_4')->nullable();
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_ai_selectable')->default(true);
            $table->enum('comparison_direction', ['higher_is_better', 'lower_is_better', 'neutral'])->nullable();
            $table->decimal('weight', 8, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_indicators');
    }
};
