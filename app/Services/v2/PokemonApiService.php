<?php

namespace App\Services\v2;

use App\Contracts\PokemonApiInterface;
use App\Models\Pokemon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class PokemonApiService implements PokemonApiInterface
{
    private string $baseUrl = 'https://pokeapi.co/api/v2/';

    private readonly Client $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function getPokemon(string|int $pokemonId): array
    {
        return $this->safeRequest('GET', $this->baseUrl.'pokemon/'.$pokemonId);
    }

    public function getPokemonList(int $limit = 20, int $offset = 0): array
    {
        return $this->safeRequest('GET', $this->baseUrl.'pokemon?limit='.$limit.'&offset='.$offset);
    }

    public function getPokemonAbility(string|int $ability): array
    {
        return $this->safeRequest('GET', $this->baseUrl.'ability/'.$ability);
    }

    public function fetchFromUrl(string $url): array
    {
        return $this->safeRequest('GET', $this->baseUrl.$url);
    }

    public function pushPokemon(Pokemon $pokemon): void
    {
        // Mock implementation - in a real scenario, this would send data to the API
        // For now, we'll just log that we would have sent the Pokemon data
        $pokemonData = $pokemon->with('abilities')->first();

        logger('Mock: Would have pushed Pokemon data to API', [
            'pokemon_id' => $pokemon->id,
            'pokemon_name' => $pokemon->name,
            'abilities' => $pokemonData['abilities'],
        ]);

        $pokemon->pushed_at = now();
        $pokemon->save();
    }

    public function getAbilityTranslations(string $abilityName): array
    {
        $abilityApiData = $this->getPokemonAbility($abilityName);

        $nameTranslations = [];
        foreach ($abilityApiData['names'] as $nameEntry) {
            $lang = $nameEntry['language']['name'];
            $nameTranslations[$lang] = $nameEntry['name'];
        }

        $effectTranslations = [];
        foreach ($abilityApiData['effect_entries'] as $effectEntry) {
            $lang = $effectEntry['language']['name'];
            $effectTranslations[$lang] = $effectEntry['effect'];
        }

        return [
            'id' => $abilityApiData['id'],
            'is_main_series' => $abilityApiData['is_main_series'],
            'name' => $nameTranslations,
            'effect_entries' => $effectTranslations,
        ];
    }

    private function safeRequest(string $method, string $uri): array
    {
        try {
            $response = $this->client->request($method, $uri);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;

            return [
                'error' => true,
                'message' => 'Pokemon API request failed',
                'details' => $e->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }
}
