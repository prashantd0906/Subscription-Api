<?php

namespace App\Services;

use App\Interfaces\UserActivityRepositoryInterface;

class UserActivityService
{
    public function __construct(
        protected UserActivityRepositoryInterface $repo
    ) {}

    public function log(int $userId, string $action, string $description): void
    {
        $this->repo->log($userId, $action, $description);
    }
    public function getUserActivities(int $userId)
    {
        return $this->repo->getByUserId($userId);
    }

    public function getAllActivities(?string $action = null, ?string $startDate = null, ?string $endDate = null)
    {
        return $this->repo->getAllWithFilters($action, $startDate, $endDate);
    }
}
