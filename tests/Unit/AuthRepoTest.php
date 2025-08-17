<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepositoryTest extends TestCase
{
    use RefreshDatabase;
    protected AuthRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new AuthRepository();
    }

    public function test_register(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123'
        ];

        $user = $this->repo->register($data);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
        $this->assertTrue(Hash::check($data['password'], $user->password));
        $this->assertEquals(1, $user->role_id);
    }

    public function test_login_fails(): void
    {
        $credentials = ['email' => 'bad@example.com', 'password' => 'wrong'];

        JWTAuth::shouldReceive('attempt')->once()->andReturnFalse();

        $this->assertNull($this->repo->login($credentials));
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        JWTAuth::shouldReceive('attempt')->once()->andReturn('fake_token');
        JWTAuth::shouldReceive('user')->once()->andReturn($user);

        $result = $this->repo->login(['email' => $user->email, 'password' => 'password']);

        $this->assertSame($user->id, $result['user']->id);
        $this->assertSame('fake_token', $result['token']);
    }

    public function test_logout_invalidates_token(): void
    {
        JWTAuth::shouldReceive('getToken')->once()->andReturn('fake_token');
        JWTAuth::shouldReceive('invalidate')->once()->with('fake_token');

        $this->assertTrue($this->repo->logout());
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        JWTAuth::shouldReceive('user')->once()->andReturn($user);

        $this->assertSame($user->id, $this->repo->me()->id);
    }
}
