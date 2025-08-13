<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\NewSubscriptionNotification;
use Exception;

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
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $finalPrice = $plan->price;

        // Cancel existing active subscription
        $this->subscriptionRepository->cancelActiveSubscription(Auth::id());

        // Create new subscription record
        $subscription = $this->subscriptionRepository->create([
            'user_id'    => Auth::id(),
            'plan_id'    => $plan->id,
            'price'      => $finalPrice,
            'start_date' => now(),
            'end_date'   => now()->copy()->addDays($plan->duration),
            'status'     => 'active',
        ]);

        // Apply promo code if provided
        if (!empty($data['promo_code'])) {
            $promo = $this->promoCodeService->validatePromoCode($data['promo_code']);

            if (!$promo) {
                // Delete the subscription
                $subscription->delete();
                return [
                    'success' => false,
                    'message' => 'Invalid or expired promo code.'
                ];
            }

            $discountAmount = ($finalPrice * $promo->discount) / 100;
            $finalPrice -= $discountAmount;

            $subscription->update(['price' => $finalPrice]);
            $subscription->promoCodes()->attach($promo->id, ['used_at' => now()]);
        }

        //Send notification to admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(
                new NewSubscriptionNotification(
                    Auth::user()->name,
                    $plan->name
                )
            );
        }

        return $subscription;
    }

    public function cancel(int $userId)
    {
        return $this->subscriptionRepository->cancel($userId);
    }

    public function getActive(int $userId)
    {
        return $this->subscriptionRepository->getActive($userId);
    }
}
