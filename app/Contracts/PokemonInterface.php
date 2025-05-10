<?php



namespace App\Contracts;

use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonSearchRequest;
use App\Models\Pokemon;
use Illuminate\Support\Collection;

interface PokemonInterface
{ 
    /**
     * Find or fetch a pokemon
     *
     * @param  string  $pokemonName
     */
    public function findOrFetchPokemon(string $pokemonName): Pokemon;

    /**
     * Get all pokemons
     *
     * @param  PokemonSearchRequest  $request
     */
    public function all(PokemonSearchRequest $request): Collection;

    /**
     * Get all pokemons
     *
     * @param  PokemonSearchRequest  $request
     */
    public function index(PokemonSearchRequest $request): Collection;

    /**
     * Favorite a pokemon
     *
     * @param  PokemonFavoriteRequest  $request
     */
    public function favorite(PokemonFavoriteRequest $request): void;

    /**
     * Attach a pokemon
     *
     * @param  PokemonRequest  $request
     */
    public function attach(PokemonRequest $request): Pokemon;

    /**
     * Detach a pokemon
     *
     * @param  PokemonRequest  $request
     */
    public function detach(PokemonRequest $request): Pokemon;

    /**
     * Mark a pokemon as changed
     *
     * @param  Pokemon  $pokemon
     * @param  bool  $hasChanged
     */
    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void;

    /**
     * Sync a pokemon's abilities
     *
     * @param  Pokemon  $pokemon
     * @param  array  $abilitiesData
     */
    public function syncPokemonAbilities(Pokemon $pokemon, array $abilitiesData): void;
}
