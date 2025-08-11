<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'discount',
        'valid_till',
    ];

    protected $casts = [
        'valid_till' => 'datetime',
    ];

    public function subscriptionPromoCodes()
    {
        return $this->hasMany(SubscriptionPromoCode::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_promo_codes')
            ->withTimestamps()
            ->withPivot('used_at');
    }
}
