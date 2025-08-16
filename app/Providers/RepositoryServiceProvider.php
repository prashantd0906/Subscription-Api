<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\PromoCodeRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\SubscriptionPlanRepositoryInterface;
use App\Interfaces\SubscriptionRepositoryInterface;
use App\Interfaces\UserActivityRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\PromoCodeRepository;
use App\Repositories\SubscriptionPlanRepository;
use App\Repositories\UserActivityRepository;
use App\Repositories\ReportRepository;
use App\Repositories\SubscriptionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(SubscriptionPlanRepositoryInterface::class, SubscriptionPlanRepository::class);
        $this->app->bind(UserActivityRepositoryInterface::class,UserActivityRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(PromoCodeRepositoryInterface::class, PromoCodeRepository::class);
    }

    public function boot(): void
    {
        
    }
}
