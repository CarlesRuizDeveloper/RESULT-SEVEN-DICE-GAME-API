<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{

  public function testPlayerRegisterSucces(){
    $response = $this->json('POST', 'api/players', [
        'name' => 'Carles',
        'email' => 'carles@carles.com',
        'password' => 'playerplayer',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Tu usuario ha sido creado! Adelante!']);

    $this->assertDatabaseHas('users', [
        'name' => 'lucas',
        'email' => 'lucas@example.com']);
  }
  
  public function testPlayerRegisterFailureMissingData(){
    $response = $this->json('POST', 'api/players', [
        'name' => '',
        'email' => '',
        'password' => '']);

      $response->assertStatus(422);
  }
  public function testPlayerRegisterFailureWeakPassword(){
    $response = $this->json('POST', 'api/players', [
      'name' => 'lucas',
      'email' => 'lucas@example.com',
      'password' => '123456',
    ]);

    $response->assertStatus(422);
  }


}