<?php

namespace App\Models;

use App\Contracts\PokemonInterface;
use App\Traits\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Pokemon extends Model
{
    use Filter;

    protected $fillable = [
        'name',
        'sprites',
        'height',
        'weight',
        'base_experience',
        'pokemon_id',
    ];

    protected $casts = [
        'sprites' => 'array',
    ];

    // Define which filter types map to database fields and how they should be filtered
    protected $filters = [
        'name' => [
            'field' => 'name',
            'method' => 'search'
        ],
        'height' => [
            'field' => 'height',
            'method' => 'range'
        ],
        'weight' => [
            'field' => 'weight',
            'method' => 'range'
        ],
        'base_experience' => [
            'field' => 'base_experience',
            'method' => 'range'
        ],
        'ability' => [
            'relation' => 'abilities',
            'field' => 'abilities.name',
            'method' => 'search'
        ],
        'is_favorite' => [
            'relation' => 'pokemon_user',
            'field' => 'pokemon_user.is_favorite',
            'method' => 'exact',
            'validation' => 'boolean'
        ]
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($pokemon) {
            Cache::forget('pokemon:' . $pokemon->name);
            app(PokemonInterface::class)->markAsChanged($pokemon, true);
        });
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function pokemon_user(): BelongsTo{
        return $this->belongsTo(PokemonUser::class);
    }
}