<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPromoCode extends Model
{
    use HasFactory;

    protected $table = 'subscription_promo_codes';

    protected $fillable = [
        'subscription_id',
        'promo_code_id',
    ];

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
