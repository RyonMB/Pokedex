<?php

namespace App\Http\Controllers\Api\v1;

use App\Contracts\PokemonInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PokemonResource;
use App\Http\Resources\PokemonCollection;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonSearchRequest;
use Illuminate\Http\JsonResponse;

class PokemonController extends Controller
{

    public function __construct(private PokemonInterface $pokemonService)
    {
    }

    /**
     * Get all pokemons in your pokedex
     *
     * @param Request $request
     * @return PokemonCollection
     */
    public function index(PokemonSearchRequest $request): PokemonCollection
    {
        $pokemon = $this->pokemonService->index($request);
        return new PokemonCollection($pokemon);
    }

    /**
     * Search in all available pokemons
     *
     * @param Request $request
     * @return PokemonCollection
     */
    public function all(PokemonSearchRequest $request): PokemonCollection
    {
        $pokemon = $this->pokemonService->all($request);
        return new PokemonCollection($pokemon);
    }

    /**
     * Add a pokemon to your favorites
     *
     * @param PokemonFavoriteRequest $request
     * @return JsonResponse
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
     *
     * @param PokemonRequest $request
     * @return PokemonResource
     */
    public function show(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->findOrFetchPokemon($request->pokemon);
        return new PokemonResource($pokemon);
    }

    /**
     * Add a pokemon to your pokedex
     *
     * @param PokemonRequest $request
     * @return PokemonResource
     */
    public function attach(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->attach($request);
        return new PokemonResource($pokemon);
    }

    /**
     * Remove a pokemon from your pokedex
     *
     * @param PokemonRequest $request
     * @return PokemonResource
     */
    public function detach(PokemonRequest $request): PokemonResource
    {
        $pokemon = $this->pokemonService->detach($request);
        return new PokemonResource($pokemon);
    }
}
