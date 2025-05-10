<?php



namespace App\Services\v2;

use App\Contracts\PokemonApiInterface;
use App\Models\Pokemon;
use GuzzleHttp\Client;

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
        $response = $this->client->request('GET', $this->baseUrl.'pokemon/'.$pokemonId);

        return json_decode($response->getBody(), true);
    }

    public function getPokemonList(int $limit = 20, int $offset = 0): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'pokemon?limit='.$limit.'&offset='.$offset);

        return json_decode($response->getBody(), true);
    }

    public function getPokemonAbility(string|int $ability): array
    {
        $response = $this->client->request('GET', $this->baseUrl.'ability/'.$ability);

        return json_decode($response->getBody(), true);
    }

    public function fetchFromUrl(string $url): array
    {
        $response = $this->client->request('GET', $this->baseUrl.$url);

        return json_decode($response->getBody(), true);
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
}
