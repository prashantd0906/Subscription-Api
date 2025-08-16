<?php
namespace App\Services;

use App\Interfaces\UserActivityRepositoryInterface;

class UserActivityService
{
    public function __construct(
        protected UserActivityRepositoryInterface $repo
    ) {}

    // Log a user action
    public function log(int $userId, string $action, string $description): void
    {
        $this->repo->log($userId, $action, $description);
    }

    public function getUserActivities(?int $userId = null)
    {
        return $this->repo->getUserActivities($userId);
    }
}
