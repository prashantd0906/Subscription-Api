<?php
namespace App\Interfaces;

interface ReportRepositoryInterface{
    public function getTotalUserPlan();
    public function getActiveSubscriptionsPerPlan();
    public function getMonthlyNewSubscriptions($months=6);
    public function getPlanChurnRate($months=6);
}
