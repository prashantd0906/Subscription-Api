<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SubscriptionNotification;

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
        $user = Auth::user();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthenticated.'];
        }

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        // Calculate final price with promo code
        [$finalPrice, $discountApplied, $promo, $error] = $this->applyPromo(
            $data['promo_code'] ?? null,
            (float) $plan->price
        );

        if ($error) {
            return ['success' => false, 'message' => $error];
        }

        // Cancel previous subscription
        $this->cancelPrevious($user->id);

        // Create new subscription
        $subscription = $this->createSubscription($user->id, $plan);

        // Log subscription activity
        $this->activityService->log(
            $user->id,
            'subscription_started',
            "Subscribed to plan {$plan->name}"
        );

        // Attach promo code if applied
        if ($promo) {
            $subscription->promoCodes()->attach($promo->id, ['used_at' => now()]);
        }

        // 🔔 Notify all admins
        $admins = User::where('role_id', 1)->get();
        Notification::send($admins, new SubscriptionNotification($user, $plan, 'started'));

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

            $this->activityService->log(
                $userId,
                'subscription_cancelled',
                "Cancelled subscription for plan ID {$planId}"
            );

            // 🔔 Notify admins
            $user = User::find($userId);
            $admins = User::where('role_id', 1)->get();
            Notification::send($admins, new SubscriptionNotification(
                $user,
                $subscription->plan,
                'cancelled'
            ));
        }

        return $subscription;
    }

    public function getActive(int $userId)
    {
        return $this->subscriptionRepository->getActive($userId);
    }

    // ----------------- PRIVATE METHODS -----------------

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
        $this->subscriptionRepository->cancelActiveSubscription($userId);
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
