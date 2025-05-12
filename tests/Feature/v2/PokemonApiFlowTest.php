<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class PokemonApiFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $token;

    private string $pokemonName;

    protected function setUp(): void
    {
        parent::setUp();
        // Register a user and get the token
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(200);
        $this->token = $response->json('token') ?? $response->json('data.token');
        $this->assertNotEmpty($this->token, 'No token returned from registration');

        // Call the show route to get a valid pokemon name
        $showResponse = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon=bulbasaur');
        $showResponse->assertStatus(201);
        $this->pokemonName = $showResponse->json('data.name');
        $this->assertNotEmpty($this->pokemonName, 'No pokemon name returned from show route');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_get_pokemon_list()
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/pokemon');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_get_all_pokemon()
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/all');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_attach_and_detach_pokemon()
    {
        // Attach
        $attach = $this->withToken($this->token)->postJson('/api/v1/pokemon/attach', [
            'pokemon' => $this->pokemonName,
        ]);
        $attach->assertStatus(200);
        $attach->assertJsonStructure(['data']);
        $this->assertTrue($attach->json('data.pokedex.is_in_pokedex'));
        $this->assertFalse($attach->json('data.pokedex.is_favorite'));

        // Detach
        $detach = $this->withToken($this->token)->postJson('/api/v1/pokemon/detach', [
            'pokemon' => $this->pokemonName,
        ]);
        $detach->assertStatus(200);
        $detach->assertJsonStructure(['data']);
        $this->assertFalse($detach->json('data.pokedex.is_in_pokedex'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_favorite_pokemon()
    {
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
        $this->assertTrue($show->json('data.pokedex.is_favorite'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function can_show_pokemon()
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon='.$this->pokemonName);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['name', 'sprites', 'height', 'weight', 'base_experience', 'abilities', 'pokedex']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function searching_nonexistent_pokemon_returns_404()
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search?pokemon=notapokemon');
        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_pokemon_without_name_returns_422()
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/pokemon/search');
        $response->assertStatus(422);
    }
}
