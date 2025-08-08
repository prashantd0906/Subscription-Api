<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\SubscriptionPlanRepositoryInterface;
use App\Interfaces\UserActivityRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Repositories\UserActivityRepository;
use App\Repositories\ReportRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(SubscriptionPlanRepositoryInterface::class, SubscriptionPlanRepository::class);
        $this->app->bind(UserActivityRepositoryInterface::class,UserActivityRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    public function boot(): void
    {
        
    }
}
