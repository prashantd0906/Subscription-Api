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
}
