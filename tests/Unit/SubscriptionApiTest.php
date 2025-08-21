<?php

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function user_view_subscription_plans(){

        SubscriptionPlan::factory()->count(3)->create();
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/subscription/plans');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'price']]]);
    }

    public function user_subscribe_to_a_plan(){

        $plan = SubscriptionPlan::factory()->create();
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $subscribe = ['plan_id' => $plan->id];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/subscription', $subscribe);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Subscription created successfully'
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id
        ]);
    }

    public function user_cancels_subscription(){
        
        $user = User::factory()->create();
        $plan = SubscriptionPlan::factory()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id
        ]);
        $token = JWTAuth::fromUser($user);

        $cancels = ['plan_id' => $plan->id];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/subscription/cancel', $cancels);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Subscription cancelled successfully'
            ]);
    }
}
