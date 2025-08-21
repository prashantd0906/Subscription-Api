<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(){
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $token = JWTAuth::fromUser($admin);
        return ['user' => $admin, 'token' => $token];
    }

    public function admin_can_view_total_users_per_plan(){
        $adminData = $this->adminUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminData['token'])
                         ->getJson('/api/v1/reports/total-users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['plan_id','plan_name','total_users']]]);
    }

    public function admin_can_view_active_subscriptions_per_plan(){

        $adminData = $this->adminUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminData['token'])
                         ->getJson('/api/v1/reports/active-subscriptions');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['plan_id','plan_name','active_subscriptions']]]);
    }

    public function admin_can_view_monthly_new_subscriptions(){

        $adminData = $this->adminUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminData['token'])
                         ->getJson('/api/v1/reports/monthly-new-subscriptions');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['month','new_subscriptions']]]);
    }

    public function admin_can_view_plan_churn_rate(){
        $adminData = $this->adminUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminData['token'])
                         ->getJson('/api/v1/reports/churn-rate');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['plan_id','plan_name','churn_rate']]]);
    }
}
