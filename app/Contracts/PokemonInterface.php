<?php



namespace App\Contracts;

use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonSearchRequest;
use App\Models\Pokemon;
use Illuminate\Support\Collection;

interface PokemonInterface
{
    public function findOrFetchPokemon(string $pokemonName): Pokemon;

    public function all(PokemonSearchRequest $request): Collection;

    public function index(PokemonSearchRequest $request): Collection;

    public function favorite(PokemonFavoriteRequest $request): void;

    public function attach(PokemonRequest $request): Pokemon;

    public function detach(PokemonRequest $request): Pokemon;

    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void;

    public function syncPokemonAbilities(Pokemon $pokemon, array $abilitiesData): void;
}
