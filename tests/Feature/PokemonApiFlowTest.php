<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    // Register a user and get the token
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $response->assertStatus(200);
    $this->token = $response->json('token') ?? $response->json('data.token');
    expect($this->token)->not->toBeEmpty('No token returned from registration');

    // Call the show route to get a valid pokemon name
    $showResponse = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon=bulbasaur');
    $showResponse->assertStatus(201);
    $this->pokemonName = $showResponse->json('data.name');
    expect($this->pokemonName)->not->toBeEmpty('No pokemon name returned from show route');
});

test('can get pokemon list', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/pokemon');
    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
});

test('can get all pokemon', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/all');
    $response->assertStatus(200);
    $response->assertJsonStructure(['data']);
});

test('can attach and detach pokemon', function () {
    // Attach
    $attach = $this->withToken($this->token)->postJson('/api/v1/pokemon/attach', [
        'pokemon' => $this->pokemonName,
    ]);
    $attach->assertStatus(200);
    $attach->assertJsonStructure(['data']);
    expect($attach->json('data.pokedex.is_in_pokedex'))->toBeTrue();
    expect($attach->json('data.pokedex.is_favorite'))->toBeFalse();

    // Detach
    $detach = $this->withToken($this->token)->postJson('/api/v1/pokemon/detach', [
        'pokemon' => $this->pokemonName,
    ]);
    $detach->assertStatus(200);
    $detach->assertJsonStructure(['data']);
    expect($detach->json('data.pokedex.is_in_pokedex'))->toBeFalse();
});

test('can favorite pokemon', function () {
    // Attach first to ensure it's in the pokedex
    $this->withToken($this->token)->postJson('/api/v1/pokemon/attach', [
        'pokemon' => $this->pokemonName,
    ]);

    // Favorite
    $favorite = $this->withToken($this->token)->postJson('/api/v1/pokemon/favorite', [
        'pokemon' => $this->pokemonName,
        'favorite' => true,
    ]);
    $favorite->assertStatus(200);
    $favorite->assertJsonStructure(['message']);

    // Show and check favorite status
    $show = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon='.$this->pokemonName);
    $show->assertStatus(200);
    expect($show->json('data.pokedex.is_favorite'))->toBeTrue();
});

test('can show pokemon', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon='.$this->pokemonName);
    $response->assertStatus(200);
    $response->assertJsonStructure(['data' => ['name', 'sprites', 'height', 'weight', 'base_experience', 'abilities', 'pokedex']]);
});

test('searching nonexistent pokemon returns 404', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon=notapokemon');
    $response->assertStatus(404);
});

test('search pokemon without name returns 422', function () {
    $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search');
    $response->assertStatus(422);
});
