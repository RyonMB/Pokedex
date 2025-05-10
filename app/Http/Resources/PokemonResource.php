<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PokemonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pokedex = $request->user()->pokemons()->where('pokemon.id', $this->id)->withPivot('is_favorite')->first();
        $isFavorite = $pokedex?->pivot->is_favorite;

        return [
            'name' => $this->name,
            'sprites' => $this->sprites,
            'height' => $this->height,
            'weight' => $this->weight,
            'base_experience' => $this->base_experience,
            'abilities' => AbilityCollection::make($this->abilities),
            'pokedex' => [
                'is_in_pokedex' => isset($pokedex),
                'is_favorite' => (bool) $isFavorite,
            ],
        ];
    }
}
