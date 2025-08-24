<?php

namespace App\Repositories;

use App\Models\SubscriptionPlan;
use App\Interfaces\SubscriptionPlanRepositoryInterface;

class SubscriptionPlanRepository implements SubscriptionPlanRepositoryInterface
{
    public function getAll()
    {
        return SubscriptionPlan::query()
            ->select('id', 'name', 'price', 'duration', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find($id)
    {
        return SubscriptionPlan::find($id);
    }

    public function create(array $data)
    {
        return SubscriptionPlan::create($data);
    }

    public function update($id, array $data)
    {
        $plan = SubscriptionPlan::find($id);
        if (!$plan) {
            return null;
        }
        $plan->update($data);
        return $plan;
    }

    public function delete($id)
    {
        $plan = SubscriptionPlan::find($id);
        if (!$plan) {
            return null;
        }

        $plan->delete();
        return true;
    }
}
