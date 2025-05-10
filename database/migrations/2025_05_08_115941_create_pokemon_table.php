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
        Schema::create('pokemon', function (Blueprint $table): void {
            $table->id();
            $table->integer('pokemon_id')->unique();
            $table->string('name');
            $table->integer('base_experience')->nullable();
            $table->integer('height');
            $table->integer('weight');
            $table->json('sprites');
            $table->timestamps();

            $table->index('pokemon_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};
