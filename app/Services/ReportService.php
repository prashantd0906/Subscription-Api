<?php
namespace App\Services;

use App\Interfaces\ReportRepositoryInterface;

class ReportService{
     public function __construct(protected ReportRepositoryInterface $repo) {}

     public function getTotalUserPlan(){
        return $this->repo->getTotalUserPlan();
     }

     public function getActiveSubscriptionsPerPlan(){
        return $this->repo->getActiveSubscriptionsPerPlan();
     }

     public function getMonthlyNewSubscriptions($months=6){
        return $this->repo->getMonthlyNewSubscriptions($months);
     }

     public function getPlanChurnRate($months=6){
        return $this->repo->getPlanChurnRate($months);
     }
}