<?php
namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\PromoCodeService;
use Illuminate\Support\Facades\Auth;
use Exception;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected PromoCodeService $promoCodeService
    ) {}

    public function subscribe(array $data)
    {
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $finalPrice = $plan->price;

        // Cancel the existing active subscription before creating new one
        $this->subscriptionRepository->cancelActiveSubscription(Auth::id());

        // Create the subscription record with base price
        $subscription = $this->subscriptionRepository->create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'price'   => $finalPrice,
            'start_date' => now(),
            'end_date' => now()->copy()->addDays($plan->duration),
            'status'  => 'active',
        ]);

        // If promo code present, validate and apply discount
        if (!empty($data['promo_code'])) {
            $promo = $this->promoCodeService->validatePromoCode($data['promo_code']);

            if (!$promo) {
                $subscription->delete();
                throw new Exception('Invalid or expired promo code.');
            }

            $discountAmount = ($finalPrice * $promo->discount) / 100;
            $finalPrice -= $discountAmount;

            // Update subscription price after discount
            $subscription->update(['price' => $finalPrice]);

            // Attach promo code to pivot table
            $subscription->promoCodes()->attach($promo->id, ['used_at' => now()]);
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
