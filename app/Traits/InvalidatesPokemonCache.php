<?php



namespace App\Traits;

use App\Contracts\PokemonInterface;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Cache;

trait InvalidatesPokemonCache
{
    protected static function bootInvalidatesPokemonCache()
    {
        $invalidateCache = function ($model): void {
            if ($model instanceof Pokemon) {
                $pokemon = $model;
            } elseif (method_exists($model, 'pokemon')) {
                $pokemon = $model->pokemon;
            }

            if ($pokemon) {
                Cache::tags('pokemon:'.$pokemon->name)->flush();
                app(PokemonInterface::class)->markAsChanged($pokemon, true);
            }
        };

        static::created($invalidateCache);
        static::updated($invalidateCache);
        static::deleted($invalidateCache);
    }
}
