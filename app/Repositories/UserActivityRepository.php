<?php

namespace App\Repositories;

use App\Models\UserActivity;
use App\Interfaces\UserActivityRepositoryInterface;

class UserActivityRepository implements UserActivityRepositoryInterface
{
    public function log(int $userId, string $action, string $description): void
    {
        UserActivity::create([
            'user_id'    => $userId,
            'action'     => $action,
            'description' => $description
        ]);
    }
    public function getByUserId(int $userId)
    {
        return UserActivity::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function getAllWithFilters(?string $action = null, ?string $startDate = null, ?string $endDate = null)
    {
        $query = UserActivity::query();

        if ($action) {
            $query->where('action', $action);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->latest()->get();
    }
}
