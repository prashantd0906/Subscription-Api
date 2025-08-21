<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'duration'];
    protected $hidden = ['updated_at'];

    public function promoCodes()
    {
        return $this->belongsToMany(
            PromoCode::class,
            'subscription_promo', // pivot table
            'plan_id',           
            'promo_id'  
        )->withPivot('used_at')->withTimestamps();
    }
}
