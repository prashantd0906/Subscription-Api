<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\SubscriptionNotification;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected PromoCodeService $promoCodeService
    ) {}

    public function getAllPlans()
    {
        return $this->subscriptionRepository->getAll();
    }

    public function subscribe(array $data)
    {
        $user = Auth::user();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthenticated.'];
        }

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        // Cancel previous subscription (if any)
        $this->cancelPrevious($user->id);

        // Create new subscription
        $subscription = $this->subscriptionRepository->create([
            'user_id'    => $user->id,
            'plan_id'    => $plan->id,
            'status'     => 'active',
            'start_date' => now(),
            'end_date'   => now()->addDays($plan->duration ?? 30),
        ]);

        // Notify all admins
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new SubscriptionNotification($user, $plan));
        }

        return [
            'success'      => true,
            'message'      => "Subscribed successfully to {$plan->name}.",
            'subscription' => $subscription,
        ];
    }

    public function cancel(int $userId, int $planId)
    {
        $subscription = $this->subscriptionRepository->cancel($userId, $planId);

        if ($subscription) {
            $subscription->update([
                'end_date'     => Carbon::parse($subscription->start_date)
                                       ->addDays($subscription->plan_duration),
                'cancelled_at' => now(),
                'status'       => 'cancelled',
            ]);

            // Notify admins about cancellation
            $user = User::find($userId);
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                $admin->notify(new SubscriptionNotification($user, $subscription->plan));
            }
        }

        return $subscription;
    }

    public function getActive(int $userId)
    {
        return $this->subscriptionRepository->getActive($userId);
    }

    // ----------------- PRIVATE METHODS -----------------

    private function cancelPrevious(int $userId): void
    {
        $this->subscriptionRepository->cancelActiveSubscription($userId);
    }
}
