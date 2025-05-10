<?php



use App\Http\Controllers\Api\v1\PokemonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['throttle:pokemon-api', 'auth:sanctum', 'setTranslation'])
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/pokemon', [PokemonController::class, 'index']);
        Route::get('/pokemon/all', [PokemonController::class, 'all']);
        Route::get('/pokemon/search', [PokemonController::class, 'show']);
        Route::post('/pokemon/attach', [PokemonController::class, 'attach']);
        Route::post('/pokemon/detach', [PokemonController::class, 'detach']);
        Route::post('/pokemon/favorite', [PokemonController::class, 'favorite']);
    });
