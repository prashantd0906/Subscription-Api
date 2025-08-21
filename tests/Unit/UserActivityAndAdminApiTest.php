<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserActivityAndAdminApiTest extends TestCase{

    use RefreshDatabase;
    public function user_can_view_activity()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/v1/user/activity');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data'
                 ]);
    }

    public function admin_can_view_dashboard(){

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/v1/admin/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_users',
                     'active_subscriptions'
                 ]);
    }

    public function admin_can_view_notifications(){
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/v1/admin/notifications');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data'
                 ]);
    }

    public function admin_can_manage_plans_crud(){

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $token = JWTAuth::fromUser($admin);

        // CREATE
        $createPlan= ['name' => 'Premium Plan', 'price' => 99.99];
        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->postJson('/api/v1/admin/plans', $createPlan);

        $createResponse->assertStatus(201)
                       ->assertJsonStructure(['message', 'plan' => ['id','name','price']]);

        $planId = $createResponse->json('plan.id');

        // UPDATE
        $updatePlan = ['name' => 'Updated Plan', 'price' => 199.99];
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->putJson("/api/v1/admin/plans/{$planId}", $updatePlan);

        $updateResponse->assertStatus(200)
                       ->assertJson([
                           'message' => 'Plan updated successfully'
                       ]);

        // DELETE
        $deleteResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->deleteJson("/api/v1/admin/plans/{$planId}");

        $deleteResponse->assertStatus(200)
                       ->assertJson([
                           'message' => 'Plan deleted successfully'
                       ]);
    }

    public function admin_can_view_all_user_activities(){
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/v1/admin/user-activity');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }
}
