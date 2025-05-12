<?php

namespace App\Contracts;

use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonSearchRequest;
use App\Models\Pokemon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PokemonInterface
{
    /**
     * Find or fetch a pokemon
     */
    public function findOrFetchPokemon(string $pokemonName): Pokemon;

    /**
     * Create a pokemon
     */
    public function createPokemon(array $data): Pokemon;

    /**
     * Get all pokemons
     */
    public function all(PokemonSearchRequest $request): LengthAwarePaginator;

    /**
     * Get all pokemons
     */
    public function index(PokemonSearchRequest $request): LengthAwarePaginator;

    /**
     * Favorite a pokemon
     */
    public function favorite(PokemonFavoriteRequest $request): void;

    /**
     * Attach a pokemon
     */
    public function attach(PokemonRequest $request): Pokemon;

    /**
     * Detach a pokemon
     */
    public function detach(PokemonRequest $request): Pokemon;

    /**
     * Mark a pokemon as changed
     */
    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void;

    /**
     * Sync a pokemon's abilities
     */
    public function syncPokemonAbilities(Pokemon $pokemon, array $abilitiesData): void;
}
