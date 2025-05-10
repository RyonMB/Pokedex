<?php

namespace App\Contracts;

use App\Models\Pokemon;

interface PokemonApiInterface
{
    /**
     * Get a single pokemon by ID
     * @param int $pokemonId
     */
    public function getPokemon($pokemonId): array;

    /**
     * Get a list of all pokemons
     */
    public function getPokemonList(int $limit = 20, int $offset = 0): array;

    /**
     * Get the ability of a pokemon
     * @param int $pokemonId
     */
    public function getPokemonAbility($pokemonId): array;

    /**
     * Fetch data from a URL
     */
    public function fetchFromUrl(string $url): array;

    /**
     * Push a pokemon to the API
     * @param Pokemon $pokemon
     */
    public function pushPokemon(Pokemon $pokemon): void;
}
