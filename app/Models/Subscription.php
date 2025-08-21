<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_duration',
        'start_date',
        'end_date',
        'cancelled_at',
        'status',
    ];

    protected $hidden = ['updated_at'];  // Hide fields in API responses

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function promoCodes()
    {
        return $this->belongsToMany(
            PromoCode::class,
            'subscription_promo_codes',
            'subscription_id',
            'promo_code_id'
        )->withPivot('used_at')
            ->withTimestamps();
    }
}
