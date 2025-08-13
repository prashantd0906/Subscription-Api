<?php
namespace App\Interfaces;

interface UserActivityRepositoryInterface
{
    public function log(int $userId, string $action, string $description):void;
    public function getByUserId(int $userId);

    public function getAllWithFilters(?string $action = null, ?string $startDate = null, ?string $endDate = null);
}