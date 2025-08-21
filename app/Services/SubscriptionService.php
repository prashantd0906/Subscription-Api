<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as NotificationModel;
use App\Models\UserActivity;
use Carbon\Carbon;

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

    public function subscribe(array $data): array
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthenticated.'];
        }

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        //Check if user already has an active subscription
        $existing = $this->subscriptionRepository->getActive($user->id);
        if ($existing && $existing->plan_id == $plan->id) {
            return [
                'success' => false,
                'message' => "You are already subscribed to the {$plan->name} plan."
            ];
        }

        // Apply promo code
        [$finalPrice, $discountApplied, $promo, $error] = $this->applyPromo(
            $data['promo_code'] ?? null,
            (float) $plan->price
        );

        if ($error) {
            return ['success' => false, 'message' => $error];
        }

        // Cancel previous subscription if it's a different plan
        if ($existing) {
            $this->cancelPrevious($user->id);
        }

        // Create new subscription
        $subscription = $this->createSubscription($user->id, $plan);

        // Attach promo code
        if ($promo) {
            $subscription->promoCodes()->attach($promo->id, ['used_at' => now()]);
        }

        // Log user activity
        UserActivity::create([
            'user_id'     => $user->id,
            'action'      => 'subscription_started',
            'description' => "{$user->name} subscribed to {$plan->name} plan.",
        ]);

        // Log notification for user
        NotificationModel::create([
            'user_id' => $user->id,
            'type'    => 'subscription_started',
            'message' => "{$user->name} has successfully subscribed to {$plan->name} plan.",
        ]);

        return [
            'success'          => true,
            'message'          => $promo
                ? "Subscribed successfully with promo code {$promo->code} applied."
                : "Subscribed successfully to {$plan->name}.",
            'original_price'   => $plan->price,
            'discount_applied' => $discountApplied,
            'final_price'      => $finalPrice,
            'subscription'     => $subscription,
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

            // Log user activity
            UserActivity::create([
                'user_id'    => $userId,
                'action'     => 'subscription_cancelled',
                'description' => "User {$subscription->user->name} cancelled {$subscription->plan->name} plan.",
            ]);
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
            return [$originalPrice, 0.0, null, null];
        }

        $promo = $this->promoCodeService->validatePromoCode($promoCode);
        if (!$promo) {
            return [$originalPrice, 0.0, null, 'Invalid or expired promo code.'];
        }

        $discount = ($originalPrice * (float) $promo->discount) / 100;
        $final    = max(0, $originalPrice - $discount);

        return [$final, $discount, $promo, null];
    }

    private function cancelPrevious(int $userId): void
    {
        $previous = $this->subscriptionRepository->getActive($userId);

        if (!$previous) return;

        $previous->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
            'end_date'     => now()->addDays($previous->plan_duration),
        ]);

        // Log user activity
        UserActivity::create([
            'user_id'    => $userId,
            'action'     => 'subscription_cancelled',
            'description' => "User {$previous->user->name} cancelled {$previous->plan->name} plan.",
        ]);
    }

    private function createSubscription(int $userId, SubscriptionPlan $plan)
    {
        $durationDays = (int) ($plan->duration ?? 30);
        $start = now();
        $end   = $start->copy()->addDays($durationDays);

        return $this->subscriptionRepository->create([
            'user_id'       => $userId,
            'plan_id'       => $plan->id,
            'plan_duration' => $durationDays,
            'start_date'    => $start,
            'end_date'      => $end,
            'status'        => 'active',
        ]);
    }
}
