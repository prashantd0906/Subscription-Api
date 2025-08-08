<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    public function getTotalUserPlan()
    {
        return DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->select('subscription_plans.name', DB::raw('COUNT(DISTINCT subscriptions.user_id) as total_users'))
            ->groupBy('subscription_plans.name')
            ->get();
    }

    public function getActiveSubscriptionsPerPlan()
    {
        return DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->where('subscriptions.status', 'active')
            ->select('subscription_plans.name', DB::raw('COUNT(*) as active_count'))
            ->groupBy('subscription_plans.name')
            ->get();
    }

    public function getMonthlyNewSubscriptions($months = 6)
    {
        return DB::table('subscriptions')
            ->where('start_date', '>=', now()->subMonths($months))
            ->select(DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getPlanChurnRate($months = 6)
    {
        $totalCancels = DB::table('user_activity')
            ->where('action', 'subscription_cancelled')
            ->where('created_at', '>=', now()->subMonths($months))
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as cancels')
            )
            ->groupBy('month');

        $totalSubs = DB::table('subscriptions')
            ->where('start_date', '>=', now()->subMonths($months))
            ->select(
                DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"),
                DB::raw('COUNT(*) as new_subs')
            )
            ->groupBy('month');

        return DB::table(DB::raw("({$totalCancels->toSql()}) as cancels"))
            ->mergeBindings($totalCancels)
            ->joinSub($totalSubs, 'subs', function ($join) {
                $join->on('cancels.month', '=', 'subs.month');
            })
            ->select(
                'cancels.month',
                DB::raw('ROUND((cancels.cancels / subs.new_subs) * 100, 2) as churn_rate')
            )
            ->orderBy('cancels.month')
            ->get();
    }
}
