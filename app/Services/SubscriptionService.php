<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Notifications\NewSubscriptionNotification;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected PromoCodeService $promoCodeService,
        protected UserActivityService $activityService
    ) {}

    public function getAllPlans()
    {
        return $this->subscriptionRepository->getAll();
    }

    public function subscribe(array $data): array
    {
        $userId = Auth::id();
        if (!$userId) {
            return ['success' => false, 'message' => 'Unauthenticated.'];
        }

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        // Calculate final price with promo handling
        [$finalPrice, $discountApplied, $promo, $error] = $this->applyPromo(
            $data['promo_code'] ?? null,
            (float) $plan->price
        );

        //If invalid promo, stop here
        if ($error) {
            return [
                'success' => false,
                'message' => $error,
            ];
        }

        // Cancel previous subscription if exists
        $this->cancelPrevious($userId);

        // Create new subscription
        $subscription = $this->createSubscription($userId, $plan);

        // Log subscription activity
        $this->activityService->log(
            $userId,
            'subscription_started',
            "Subscribed to plan {$plan->name}"
        );

        // Attach promo if applied
        if ($promo) {
            $subscription->promoCodes()->attach($promo->id, ['used_at' => now()]);
        }

        return [
            'success'          => true,
            'message'          => $promo
                ? "Subscribed successfully with promo code {$promo->code} applied."
                : 'Subscribed successfully',
            'original_price'   => $plan->price,
            'discount_applied' => $discountApplied,
            'final_price'      => $finalPrice,
            'subscription'     => $subscription,
        ];
    }


    public function cancel(int $userId)
    {
        $subscription = $this->subscriptionRepository->getActive($userId);

        if ($subscription) {
            $this->subscriptionRepository->cancel($userId);

            $this->activityService->log(
                $userId,
                'subscription_cancelled',
                "Cancelled subscription for plan ID {$subscription->plan_id}"
            );
        }

        return $subscription;
    }

    public function getActive(int $userId)
    {
        return $this->subscriptionRepository->getActive($userId);
    }

    private function applyPromo(?string $promoCode, float $originalPrice): array
    {
        if (!$promoCode) {
            return [$originalPrice, 0.0, null,null];
        }

        $promo = $this->promoCodeService->validatePromoCode($promoCode);
        if (!$promo) {
            return [$originalPrice, 0.0, null, 'Invalid or expired promo code.'];
        }

        $discount = ($originalPrice * (float) $promo->discount) / 100;
        $final    = max(0, $originalPrice - $discount);

        return [$final, $discount, $promo,null];
    }

    private function cancelPrevious(int $userId): void
    {
        $this->subscriptionRepository->cancelActiveSubscription($userId);
    }

    private function createSubscription(int $userId, SubscriptionPlan $plan)
    {
        return $this->subscriptionRepository->create([
            'user_id'    => $userId,
            'plan_id'    => $plan->id,
            'start_date' => now(),
            'end_date'   => now()->copy()->addDays((int) $plan->duration),
            'status'     => 'active',
        ]);
    }
}
