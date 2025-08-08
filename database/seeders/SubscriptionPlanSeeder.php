<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{

    public function run()
    {
        SubscriptionPlan::insert([
            [
                'name' => 'Basic',
                'price' => 0.00,
                'duration' => 30,
                'created_at' => now()
            ],
            [
                'name' => 'Standard',
                'price' => 500.00,
                'duration' => 30,
                'created_at' => now()
            ],
            [
                'name' => 'Premium',
                'price' => 1000.00,
                'duration' => 30,
                'created_at' => now()
            ],
        ]);
    }
}
