<?php

namespace App\Jobs;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Pokemon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;

class PushPokemonJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable, Batchable, SerializesModels;

    /**
     * The Pokemon to be processed
     */
    protected $pokemon;

    /**
     * Create a new job instance.
     */
    public function __construct(Pokemon $pokemon)
    {
        $this->pokemon = $pokemon;
    }

    /**
     * Execute the job.
     */
    public function handle(PokemonInterface $pokemonService, PokemonApiInterface $pokemonApiService): void
    {
        $pokemonApiService->pushPokemon($this->pokemon);
        $pokemonService->markAsChanged($this->pokemon, false);
    }
}
