<?php
namespace App\Repositories;

use App\Interfaces\UserActivityRepositoryInterface;
use App\Models\UserActivity;

class UserActivityRepository implements UserActivityRepositoryInterface
{

    public function log(int $userId, string $action, string $description): void
    {
        UserActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
    }
    public function getUserActivities(?int $userId = null)
    {
        $query = UserActivity::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
