<?php

namespace App\Services;

use App\Interfaces\ReportRepositoryInterface;

class ReportService
{
    public function __construct(protected ReportRepositoryInterface $repo) {}

    public function getSummary(): array
    {
        return [
            'total_users_per_plan' => $this->repo->getTotalUserPlan(),
            'active_subscriptions_per_plan' => $this->repo->getActiveSubscriptionsPerPlan(),
            'monthly_new_subscriptions' => $this->repo->getMonthlyNewSubscriptions(6),
            'churn_rate_per_plan' => $this->repo->getPlanChurnRate(6),
        ];
    }
}
