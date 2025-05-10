<?php



namespace App\Jobs;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use App\Models\Pokemon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class GetPokemonDataJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Pokemon $pokemon) {}

    /**
     * Execute the job.
     */
    public function handle(PokemonApiInterface $pokeApi, PokemonInterface $pokemonService): void
    {
        try {
            // Fetch fresh data from the API
            $data = $pokeApi->getPokemon($this->pokemon->name);

            // Gather all ability translations
            $abilitiesData = [];
            foreach ($data['abilities'] as $abilityData) {
                $abilitiesData[] = $pokeApi->getAbilityTranslations($abilityData['ability']['name']);
            }

            // Sync abilities using the service
            $pokemonService->syncPokemonAbilities($this->pokemon, $abilitiesData);

        } catch (Throwable $e) {
            Log::error('Failed to fetch/sync Pokemon data', [
                'pokemon_id' => $this->pokemon->id,
                'pokemon_name' => $this->pokemon->name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
