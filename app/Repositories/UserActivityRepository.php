<?php

namespace App\Repositories;

use App\Interfaces\UserActivityRepositoryInterface;
use App\Models\UserActivity;
use App\Models\User;

class UserActivityRepository implements UserActivityRepositoryInterface
{
    public function log(int $userId, string $action, string $description): void
    {
        $user = User::find($userId); //fetch user by ID

        $userName = $user ? $user->name : 'Unknown User'; //If user not found

        UserActivity::create([
            'user_id'    => $userId,
            'action'     => $action,
            'description' => $userName . ' ' . $description,
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
