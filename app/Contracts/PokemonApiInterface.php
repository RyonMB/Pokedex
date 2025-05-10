<?php

namespace App\Contracts;

use App\Models\Pokemon;

interface PokemonApiInterface
{
    /**
     * Get a single pokemon by ID
     */
    public function getPokemon(string|int $pokemonId): array;

    /**
     * Get a list of all pokemons
     */
    public function getPokemonList(int $limit = 20, int $offset = 0): array;

    /**
     * Get the ability of a pokemon
     *
     * @param  string|int  $abilityId
     */
    public function getPokemonAbility(string|int $ability): array;

    /**
     * Fetch data from a URL
     */
    public function fetchFromUrl(string $url): array;

    /**
     * Push a pokemon to the API
     */
    public function pushPokemon(Pokemon $pokemon): void;

    /**
     * Get ability translations and meta for a given ability name
     *
     * @return array ['id' => int, 'is_main_series' => bool, 'name' => [...], 'effect_entries' => [...]]
     */
    public function getAbilityTranslations(string $abilityName): array;
}
