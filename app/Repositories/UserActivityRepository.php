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
}
