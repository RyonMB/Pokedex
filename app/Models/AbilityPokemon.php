<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbilityPokemon extends Pivot
{
    protected $table = 'ability_pokemon';

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
