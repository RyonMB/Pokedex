<?php

namespace App\Http\Controllers\Api\v1;

use App\Contracts\PokemonInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonSearchRequest;
use App\Http\Resources\PokemonCollection;
use App\Http\Resources\PokemonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PokemonController extends Controller
{
    public function __construct(private readonly PokemonInterface $pokemonService) {}

    /**
     * Get all pokemons in your pokedex
     *
     * @param  Request  $request
     */
    public function index(PokemonSearchRequest $request): PokemonCollection
    {
        $pokemon = $this->pokemonService->index($request);

        return new PokemonCollection($pokemon);
    }

    /**
     * Search in all available pokemons
     *
     * @param  Request  $request
     */
    public function all(PokemonSearchRequest $request): PokemonCollection
    {
        $pokemon = $this->pokemonService->all($request);

        return new PokemonCollection($pokemon);
    }

    /**
     * Add a pokemon to your favorites
     */
    public function favorite(PokemonFavoriteRequest $request): JsonResponse
    {
        $this->pokemonService->favorite($request);

        return response()->json([
            'message' => $request->favorite ? 'Pokemon added to favorites' : 'Pokemon removed from favorites',
        ]);
    }

    /**
     * Get a pokemon by name
     */
    public function show(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->findOrFetchPokemon($request->pokemon, $request->user()->language);

        return new PokemonResource($pokemon);
    }

    /**
     * Add a pokemon to your pokedex
     */
    public function attach(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->attach($request);

        return new PokemonResource($pokemon);
    }

    /**
     * Remove a pokemon from your pokedex
     */
    public function detach(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->detach($request);

        return new PokemonResource($pokemon);
    }
}
