<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function user_register()
    {
        $register = [
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
            'password' => 'john123',
            'password_confirmation' => 'john123'
        ];
        $response = $this->postJson('/api/v1/auth/register', $register);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email']
            ]);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }

    public function user_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);
        $login = [
            'email' => $user->email,
            'password' => 'password123'
        ];
        $response = $this->postJson('/api/v1/auth/login', $login);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }
    public function auth_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }
    public function user_logout(){
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }
}
