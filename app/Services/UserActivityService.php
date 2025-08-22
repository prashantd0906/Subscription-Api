<?php

namespace App\Services;

use App\Interfaces\UserActivityRepositoryInterface;

class UserActivityService
{
    public function __construct(
        protected UserActivityRepositoryInterface $repo
    ) {}

    public function getAll()
    {
        return $this->repo->getUserActivities();
    }

    public function log(int $userId, string $action, string $description): void
    {
        $this->repo->log($userId, $action, $description);
    }

    public function getUserActivities(?int $userId = null)
    {
        return $this->repo->getUserActivities($userId);
    }
}
