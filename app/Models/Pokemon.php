<?php

namespace App\Models;

use App\Traits\Filter;
use App\Traits\InvalidatesPokemonCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Pokemon extends Model
{
    use Filter, InvalidatesPokemonCache;

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
            'method' => 'search',
        ],
        'height' => [
            'field' => 'height',
            'method' => 'range',
        ],
        'weight' => [
            'field' => 'weight',
            'method' => 'range',
        ],
        'base_experience' => [
            'field' => 'base_experience',
            'method' => 'range',
        ],
        'ability' => [
            'relation' => 'abilities',
            'field' => 'abilities.name',
            'method' => 'search',
        ],
        'is_favorite' => [
            'relation' => 'users',
            'field' => 'is_favorite',
            'method' => 'exact',
            'validation' => 'boolean',
        ],
    ];

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_favorite');
    }
}
