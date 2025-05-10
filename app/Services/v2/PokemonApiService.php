<?php

namespace App\Services\v2;

use App\Contracts\PokemonApiInterface;
use GuzzleHttp\Client;
use App\Models\Pokemon;

class PokemonApiService implements PokemonApiInterface
{

    private $baseUrl;
    private $client;

    public function __construct(){
        $this->baseUrl = 'https://pokeapi.co/api/v2/';
        $this->client = new Client();
    }

    public function getPokemon($pokemonId): array
    {
        $response = $this->client->request('GET', $this->baseUrl . 'pokemon/' . $pokemonId);
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function getPokemonList(int $limit = 20, int $offset = 0): array
    {
        $response = $this->client->request('GET', $this->baseUrl . 'pokemon?limit=' . $limit . '&offset=' . $offset);
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function getPokemonAbility($pokemonId): array
    {
        $response = $this->client->request('GET', $this->baseUrl . 'ability/' . $pokemonId);
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function fetchFromUrl(string $url): array
    {
        $response = $this->client->request('GET', $this->baseUrl . $url);
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public function pushPokemon(Pokemon $pokemon): void
    {
        // Mock implementation - in a real scenario, this would send data to the API
        // For now, we'll just log that we would have sent the Pokemon data
        $pokemonData = $pokemon->with('abilities')->first();

        logger('Mock: Would have pushed Pokemon data to API', [
            'pokemon_id' => $pokemon->id,
            'pokemon_name' => $pokemon->name,
            'abilities' => $pokemonData['abilities']
        ]);

        $pokemon->pushed_at = now();
        $pokemon->save();
    }
}