<?php

namespace Tests\Unit;

use App\Models\Ability;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PokemonFilterTraitTest extends TestCase
{
    use RefreshDatabase;

    private Pokemon $pokemon1;

    private Pokemon $pokemon2;

    private Pokemon $pokemon3;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
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
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_by_name()
    {
        $result = Pokemon::filter(['filters' => ['name' => 'char']])->get();
        $this->assertEquals(1, $result->count());
        $this->assertEquals('charizard', $result->first()->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_by_height_range()
    {
        $result = Pokemon::filter(['filters' => ['height' => ['min' => 6, 'max' => 18]]])->get();
        $this->assertEquals(2, $result->count());
        $this->assertContains('charizard', $result->pluck('name')->toArray());
        $this->assertContains('bulbasaur', $result->pluck('name')->toArray());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_by_ability()
    {
        $result = Pokemon::filter(['filters' => ['ability' => 'Fire']])->get();
        $this->assertEquals(1, $result->count());
        $this->assertEquals('charizard', $result->first()->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_by_favorite_status()
    {
        // Test using the scope with the authenticated user
        $this->actingAs($this->user);

        $result = Pokemon::filter(['filters' => ['is_favorite' => true]])->get();
        $this->assertEquals(1, $result->count());
        $this->assertEquals('charizard', $result->first()->name);

        $result = Pokemon::filter(['filters' => ['is_favorite' => false]])->get();
        $this->assertEquals(1, $result->count());
        $this->assertEquals('squirtle', $result->first()->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_multiple_filters_combined()
    {
        $this->actingAs($this->user);

        $result = Pokemon::filter([
            'filters' => [
                'height' => ['min' => 1, 'max' => 100],
                'is_favorite' => true,
            ],
        ])->get();

        $this->assertEquals(1, $result->count());
        $this->assertEquals('charizard', $result->first()->name);
    }
}
