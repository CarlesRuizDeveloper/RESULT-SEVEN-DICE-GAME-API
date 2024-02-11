<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserAngGameTest extends TestCase
{

  public function testPlayerRegisterSuccess()
  {
      $response = $this->json('POST', 'api/players', [
          'name' => 'Cafewree3efdeed',
          'email' => 'Cafewree3efdeed@carles.com',
          'password' => 'Cafee3efdeed',
      ]);
  
      $response->assertStatus(201);
      
      $this->assertCredentials([
          'name' => 'Cafewree3efdeed',
          'email' => 'Cafewree3efdeed@carles.com',
          'password' => 'Cafee3efdeed'
      ]);
  }

  public function testGetAllPlayers()
  {
      Passport::actingAs(User::factory()->create());

      $response = $this->json('GET', 'api/players');

      $response->assertStatus(200);
  }

  public function testUpdatePlayer()
  {
      Passport::actingAs(User::factory()->create());

      $user = User::factory()->create();

      $response = $this->json('PUT', "api/players/{$user->id}", [
          'name' => 'New Name',
      ]);

      $response->assertStatus(200);
      $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
  }

  public function testGetRankingWithDetails()
  {
      Passport::actingAs(User::factory()->create());

      $response = $this->json('GET', 'api/players/ranking');

      $response->assertStatus(200);
  }

  // GameController

  public function testRollDice()
  {
      $user = User::factory()->create();

      $response = $this->actingAs($user, 'api')->json('POST', "api/players/{$user->id}/games");

      $response->assertStatus(201);
      $response->assertJsonStructure(['id', 'user_id', 'dice1', 'dice2', 'win']);
  }

  public function testDeleteAllGames()
  {
      $user = User::factory()->create();
      Game::factory()->count(5)->create(['user_id' => $user->id]);

      $response = $this->actingAs($user, 'api')->json('DELETE', "api/players/{$user->id}/games");

      $response->assertStatus(200);
      $this->assertEquals(0, Game::where('user_id', $user->id)->count());
  }

  public function testGetGames()
  {
      $user = User::factory()->create();
      Game::factory()->count(3)->create(['user_id' => $user->id]);

      $response = $this->actingAs($user, 'api')->json('GET', "api/players/{$user->id}/games");

      $response->assertStatus(200);
  }

  public function testGetLoser()
  {
      $response = $this->actingAs(User::factory()->create(), 'api')->json('GET', 'api/players/ranking/loser');

      $response->assertStatus(200);
  }

  public function testGetWinner()
  {
      $response = $this->actingAs(User::factory()->create(), 'api')->json('GET', 'api/players/ranking/winner');

      $response->assertStatus(200);
  }
}
  
  



