<?php

use App\Models\Ability;
use App\Models\Pokemon;
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
        Schema::create('abilities', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->boolean('is_main_series')->default(false);
            $table->json('effect_entries');
            $table->timestamps();
        });

        Schema::create('ability_pokemon', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Ability::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Pokemon::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ability_pokemon');
        Schema::dropIfExists('abilities');
    }
};
