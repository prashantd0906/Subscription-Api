<?php
namespace App\Interfaces;

interface UserActivityRepositoryInterface
{
    public function log(int $userId, string $action, string $description):void;
}