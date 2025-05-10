<?php

namespace App\Jobs;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use App\Models\Pokemon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

final class PushPokemonJob implements ShouldBeUnique, ShouldQueue
{
    use Batchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        /**
         * The Pokemon to be processed
         */
        private Pokemon $pokemon
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PokemonInterface $pokemonService, PokemonApiInterface $pokemonApiService): void
    {
        $pokemonApiService->pushPokemon($this->pokemon);
        $pokemonService->markAsChanged($this->pokemon, false);
    }
}
