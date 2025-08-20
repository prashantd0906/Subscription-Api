<?php
namespace App\Repositories;

use App\Interfaces\UserActivityRepositoryInterface;
use App\Models\UserActivity;
use App\Models\User;

class UserActivityRepository implements UserActivityRepositoryInterface
{
    public function log(int $userId, string $action, string $description): void
    {
        //fetch user by ID
        $user = User::find($userId);

        //If user not found
        $userName = $user ? $user->name : 'Unknown User';

        UserActivity::create([
            'user_id'    => $userId,
            'action'     => $action,
            'description'=> $userName . ' ' . $description,
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

    public function getAll()
    {
        return $this->getUserActivities();
    }
}
