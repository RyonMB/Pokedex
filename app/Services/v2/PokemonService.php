<?php



namespace App\Services\v2;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use App\Http\Requests\PokemonFavoriteRequest;
use App\Http\Requests\PokemonRequest;
use App\Http\Requests\PokemonSearchRequest;
use App\Jobs\GetPokemonDataJob;
use App\Models\Ability;
use App\Models\Pokemon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final readonly class PokemonService implements PokemonInterface
{
    public function __construct(private PokemonApiInterface $api) {}

    public function findOrFetchPokemon(string $pokemonName, string $locale = 'en'): Pokemon
    {
        $cacheKey = "pokemon:$pokemonName-$locale";
        $cacheTag = "pokemon:$pokemonName";

        // Try to get from cache
        $pokemon = Cache::tags($cacheTag)->get($cacheKey);
        if ($pokemon) {
            return $pokemon;
        }
        
        // Try to get from database
        $pokemon = Pokemon::where('name', $pokemonName)->with('abilities')->first();
        if ($pokemon) {
            Cache::tags($cacheTag)->put($cacheKey, $pokemon, 600);
            return $pokemon;
        }
        
        // Fetch from API and create new record

        $apiData = $this->api->getPokemon($pokemonName);

        $pokemon = Pokemon::create([
            'pokemon_id' => $apiData['id'],
            'name' => $apiData['name'],
            'sprites' => $apiData['sprites'],
            'height' => $apiData['height'],
            'weight' => $apiData['weight'],
            'base_experience' => $apiData['base_experience'],
        ]);

        GetPokemonDataJob::dispatch($pokemon);

        return $pokemon;
    }

    public function all(PokemonSearchRequest $request): Collection
    {
        return Pokemon::filter($request)->all();
    }

    public function index(PokemonSearchRequest $request): Collection
    {
        return $request->user()->pokemons()->filter($request->all())->orderByPivot('is_favorite', 'desc')->paginate(20);
    }

    public function favorite(PokemonFavoriteRequest $request): void
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->syncWithoutDetaching([$pokemon->id => ['is_favorite' => $request->favorite]]);
    }

    public function attach(PokemonRequest $request): Pokemon
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->syncWithoutDetaching($pokemon);

        return $pokemon;
    }

    public function detach(PokemonRequest $request): Pokemon
    {
        $pokemon = $this->findOrFetchPokemon($request->pokemon);
        $request->user()->pokemons()->detach($pokemon);

        return $pokemon;
    }

    public function markAsChanged(Pokemon $pokemon, bool $hasChanged): void
    {
        $pokemon->has_changed = $hasChanged;
        $pokemon->save();
    }

    public function syncPokemonAbilities(Pokemon $pokemon, array $abilitiesData): void
    {
        $abilityIds = [];
        foreach ($abilitiesData as $abilityData) {
            $ability = Ability::updateOrCreate(
                ['id' => $abilityData['id'], 'name' => $abilityData['name']],
                ['is_main_series' => $abilityData['is_main_series']]
            );
            foreach ($abilityData['name'] as $lang => $value) {
                $ability->setTranslation('name', $lang, $value);
            }
            foreach ($abilityData['effect_entries'] as $lang => $value) {
                $ability->setTranslation('effect_entries', $lang, $value);
            }
            $ability->save();
            $abilityIds[] = $ability->id;
        }
        $pokemon->abilities()->sync($abilityIds);
    }
}
