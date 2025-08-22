<?php

namespace App\Services;

use App\Interfaces\SubscriptionPlanRepositoryInterface;

class SubscriptionPlanService
{
    public function __construct(protected SubscriptionPlanRepositoryInterface $repo) {}


    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $result = $this->repo->delete($id);

        if (!$result) {
            return [
                'success' => false,
                'message' => "Subscription plan with id {$id} not found.",
                'data'    => null
            ];
        }

        return [
            'success' => true,
            'message' => 'Plan deleted successfully',
            'data'    => null
        ];
    }
}
