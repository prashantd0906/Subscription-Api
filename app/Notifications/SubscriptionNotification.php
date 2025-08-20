<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected $user,
        protected $plan
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "{$this->user->name} subscribed to {$this->plan->name}",
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
        ];
    }
}
