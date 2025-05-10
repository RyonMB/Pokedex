<?php

namespace App\Models;

use App\Traits\InvalidatesPokemonCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class AbilityPokemon extends Pivot
{
    use InvalidatesPokemonCache;

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
