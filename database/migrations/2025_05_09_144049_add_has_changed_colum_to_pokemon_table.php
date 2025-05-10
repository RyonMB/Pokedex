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
        Schema::table('pokemon', function (Blueprint $table) {
            $table->boolean('has_changed')->after('pokemon_id')->default(true);
            $table->dateTime('pushed_at')->after('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokemon', function (Blueprint $table) {
            $table->dropColumn('has_changed');
            $table->dropColumn('pushed_at');
        });
    }
};
