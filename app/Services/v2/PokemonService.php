<?php

namespace App\Services\v2;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use App\Models\Pokemon;
use App\Http\Requests\PokemonSearchRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;

class PokemonService implements PokemonInterface
{


    public function __construct(private PokemonApiInterface $api)
    {
    }

    public function findOrFetchPokemon(string $pokemonName): Pokemon
    {
        $pokemon = Cache::remember('pokemon:' . $pokemonName, 600, function () use ($pokemonName) {
            return Pokemon::where('name', $pokemonName)->first();
        });

        if (!$pokemon) {
            $pokemon = $this->api->getPokemon($pokemonName);

            $pokemon = Pokemon::create([
                'pokemon_id' => $pokemon['id'],
                'name' => $pokemon['name'],
                'sprites' => $pokemon['sprites'],
                'height' => $pokemon['height'],
                'weight' => $pokemon['weight'],
                'base_experience' => $pokemon['base_experience'],
            ]);
        }

        return $pokemon;
    }

    public function all(PokemonSearchRequest $request): Collection
    {
        return Pokemon::filter($request)->all();
    }

    public function index(PokemonSearchRequest $request): Collection
    {
        return $request->user()->pokemons()->filter($request->all())->orderByPivot('is_favorite', 'desc')->paginate(20);
    }

    public function favorite(PokemonFavoriteRequest $request): void
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->syncWithoutDetaching([$pokemon->id => ['is_favorite' => $request->favorite]]);
    }

    public function attach(PokemonRequest $request): Pokemon
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->syncWithoutDetaching($pokemon);
        return $pokemon;
    }

    public function detach(PokemonRequest $request): Pokemon
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->detach($pokemon);
        return $pokemon;
    }

    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void
    {
        $pokemon->has_changed = $hasChanged;
        $pokemon->save();
    }
}