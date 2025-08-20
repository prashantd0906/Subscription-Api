<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionActivityNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $plan;
    protected $action; // subscribed | cancelled

    public function __construct($user, $plan, string $action)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'subscriber_name' => $this->user->name,
            'plan_name'       => $this->plan->name,
            'action'          => $this->action,
            'timestamp'       => now()->toDateTimeString(),
        ];
    }
}
