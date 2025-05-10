<?php

namespace App\Contracts;

use App\Models\Pokemon;
use Illuminate\Support\Collection;
use App\Http\Requests\PokemonSearchRequest;
use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
interface PokemonInterface
{
    public function findOrFetchPokemon(string $pokemonName): Pokemon;
    public function all(PokemonSearchRequest $request): Collection;
    public function index(PokemonSearchRequest $request): Collection;
    public function favorite(PokemonFavoriteRequest $request): void;
    public function attach(PokemonRequest $request): Pokemon;
    public function detach(PokemonRequest $request): Pokemon;
    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void;
}
