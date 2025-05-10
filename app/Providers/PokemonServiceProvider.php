<?php

namespace App\Providers;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\v2\PokemonApiService;
use App\Services\v2\PokemonService;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class PokemonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PokemonApiInterface::class, function($app){
            $apiVersion = config('services.pokemon.api_version');

            return match($apiVersion){
                'v2' => new PokemonApiService(),
                default => throw new \Exception("Unsupported API version: {$apiVersion}")
            };
        });

        $this->app->singleton(PokemonInterface::class, function($app){
            $apiVersion = config('services.pokemon.api_version');

            return match($apiVersion){
                'v2' => new PokemonService($app->make(PokemonApiService::class)),
                default => throw new \Exception("Unsupported API version: {$apiVersion}")
            };
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('pokemon-api', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });
    }
}
