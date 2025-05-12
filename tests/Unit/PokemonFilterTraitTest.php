<?php

use App\Models\Ability;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Laravel\actingAs;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    /** @var \App\Models\User $user */
    $this->user = User::factory()->create();

    // Create abilities
    $fireAbility = Ability::create([
        'name' => ['en' => 'Fire', 'nl' => 'Vuur'],
        'is_main_series' => true,
        'effect_entries' => ['en' => 'Burns targets', 'nl' => 'Verbrandt doelwitten'],
    ]);

    $waterAbility = Ability::create([
        'name' => ['en' => 'Water', 'nl' => 'Water'],
        'is_main_series' => true,
        'effect_entries' => ['en' => 'Soaks targets', 'nl' => 'Doorweekt doelwitten'],
    ]);

    // Create three test Pokemon with different attributes
    $this->pokemon1 = Pokemon::create([
        'name' => 'charizard',
        'sprites' => ['front_default' => 'url1'],
        'height' => 17,
        'weight' => 905,
        'base_experience' => 240,
        'pokemon_id' => 6,
    ]);

    $this->pokemon2 = Pokemon::create([
        'name' => 'squirtle',
        'sprites' => ['front_default' => 'url2'],
        'height' => 5,
        'weight' => 90,
        'base_experience' => 63,
        'pokemon_id' => 7,
    ]);

    $this->pokemon3 = Pokemon::create([
        'name' => 'bulbasaur',
        'sprites' => ['front_default' => 'url3'],
        'height' => 7,
        'weight' => 69,
        'base_experience' => 64,
        'pokemon_id' => 1,
    ]);

    // Assign abilities
    $this->pokemon1->abilities()->attach($fireAbility);
    $this->pokemon2->abilities()->attach($waterAbility);

    // Add pokemon to user's pokedex with different favorite statuses
    $this->user->pokemons()->attach($this->pokemon1, ['is_favorite' => true]);
    $this->user->pokemons()->attach($this->pokemon2, ['is_favorite' => false]);
    // pokemon3 is not in user's pokedex
});

test('filter by name', function () {
    $result = Pokemon::filter(['filters' => ['name' => 'char']])->get();
    expect($result->count())->toBe(1);
    expect($result->first()->name)->toBe('charizard');
});

test('filter by height range', function () {
    $result = Pokemon::filter(['filters' => ['height' => ['min' => 6, 'max' => 18]]])->get();
    expect($result->count())->toBe(2);
    expect($result->pluck('name')->toArray())->toContain('charizard');
    expect($result->pluck('name')->toArray())->toContain('bulbasaur');
});

test('filter by ability', function () {
    $result = Pokemon::filter(['filters' => ['ability' => 'Fire']])->get();
    expect($result->count())->toBe(1);
    expect($result->first()->name)->toBe('charizard');
});

test('filter by favorite status', function () {
    // Test using the scope with the authenticated user
    $this->actingAs($this->user);

    $result = Pokemon::filter(['filters' => ['is_favorite' => true]])->get();
    expect($result->count())->toBe(1);
    expect($result->first()->name)->toBe('charizard');

    $result = Pokemon::filter(['filters' => ['is_favorite' => false]])->get();
    expect($result->count())->toBe(1);
    expect($result->first()->name)->toBe('squirtle');
});

test('multiple filters combined', function () {
    $this->actingAs($this->user);

    $result = Pokemon::filter([
        'filters' => [
            'height' => ['min' => 1, 'max' => 100],
            'is_favorite' => true,
        ],
    ])->get();

    expect($result->count())->toBe(1);
    expect($result->first()->name)->toBe('charizard');
});
