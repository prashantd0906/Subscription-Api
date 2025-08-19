<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionPlanRepository;

class SubscriptionPlanRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionPlanRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new SubscriptionPlanRepository();
    }

    public function it_returns_empty_collection_if_no_plans_exist()
    {
        $plans = $this->repo->getAll();

        $this->assertTrue($plans->isEmpty());
    }

    public function it_returns_all_subscription_plans()
    {
        //created 2 plans
        SubscriptionPlan::factory()->create(['name' => 'Basic Plan']);
        SubscriptionPlan::factory()->create(['name' => 'Pro Plan']);

        // Fetch
        $plans = $this->repo->getAll();

        // Assert
        $this->assertCount(2, $plans);
        $this->assertEquals('Basic Plan', $plans->first()->name);
        $this->assertInstanceOf(SubscriptionPlan::class, $plans->first());
    }

    public function it_returns_a_plan_when_found_by_id()
    {
        $plan = SubscriptionPlan::factory()->create(['name' => 'Basic Plan']);

        // Act
        $found = $this->repo->find($plan->id);

        // Assert
        $this->assertInstanceOf(SubscriptionPlan::class, $found);
        $this->assertEquals('Basic Plan', $found->name);
        $this->assertEquals($plan->id, $found->id);
    }

    public function it_creates_a_new_subscription_plan()
    {
        //inputs
        $data = [
            'name' => 'Premium Plan',
            'price' => 199.99,
            'duration' => 30,
        ];

        // Fetch
        $plan = $this->repo->create($data);

        // Assert
        $this->assertInstanceOf(SubscriptionPlan::class, $plan);
        $this->assertEquals('Premium Plan', $plan->name);
        $this->assertEquals(199.99, $plan->price);
        $this->assertEquals(30, $plan->duration);

        //check DB
        $this->assertDatabaseHas('subscription_plans', [
            'name' => 'Premium Plan',
            'price' => 199.99,
            'duration' => 30,
        ]);
    }

    public function it_updates_an_existing_subscription_plan()
    {
        //create plan
        $plan = SubscriptionPlan::factory()->create([
            'name' => 'Basic Plan',
            'price' => 99.99,
            'duration' => 30,
        ]);

        $updateData = [
            'name' => 'Updated Plan',
            'price' => 149.99,
            'duration' => 60,
        ];

        // Fetch
        $updated = $this->repo->update($plan->id, $updateData);

        // Assert
        $this->assertEquals('Updated Plan', $updated->name);
        $this->assertEquals(149.99, $updated->price);
        $this->assertEquals(60, $updated->duration);

        // Check DB
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'price' => 149.99,
            'duration' => 60,
        ]);
    }

    public function it_deletes_an_existing_subscription_plan()
    {
        //createpla n
        $plan = SubscriptionPlan::factory()->create();

        //delete it
        $deleted = $this->repo->delete($plan->id);

        //one record deleted
        $this->assertEquals(1, $deleted);
        $this->assertDatabaseMissing('subscription_plans', ['id' => $plan->id]);
    }

}
