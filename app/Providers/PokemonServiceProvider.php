<?php



namespace App\Providers;

use App\Contracts\PokemonApiInterface;
use App\Contracts\PokemonInterface;
use App\Services\v2\PokemonApiService;
use App\Services\v2\PokemonService;
use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

final class PokemonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PokemonApiInterface::class, function ($app): PokemonApiService {
            $apiVersion = config('services.pokemon.api_version');

            return match ($apiVersion) {
                'v2' => new PokemonApiService,
                default => throw new Exception("Unsupported API version: {$apiVersion}")
            };
        });

        $this->app->singleton(PokemonInterface::class, function ($app): PokemonService {
            $apiVersion = config('services.pokemon.api_version');

            return match ($apiVersion) {
                'v2' => new PokemonService($app->make(PokemonApiService::class)),
                default => throw new Exception("Unsupported API version: {$apiVersion}")
            };
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('pokemon-api', fn (Request $request) => Limit::perMinute(100)->by($request->user()?->id ?: $request->ip()));

        Translatable::fallback(
            fallbackLocale: 'en',
        );
    }
}
