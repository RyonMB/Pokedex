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
        $pokemon = Cache::tags('pokemon:'.$pokemonName)->remember('pokemon:'.$pokemonName.'-'.$locale, 600, fn () => Pokemon::where('name', $pokemonName)->with('abilities')->first());

        if (! $pokemon) {
            $pokemon = $this->api->getPokemon($pokemonName);

            $pokemon = Pokemon::create([
                'pokemon_id' => $pokemon['id'],
                'name' => $pokemon['name'],
                'sprites' => $pokemon['sprites'],
                'height' => $pokemon['height'],
                'weight' => $pokemon['weight'],
                'base_experience' => $pokemon['base_experience'],
            ]);

            GetPokemonDataJob::dispatch($pokemon);
        }

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
