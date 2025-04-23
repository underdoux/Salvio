<?php

namespace App\Services;

use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function sendOrderStatusNotification($order, $status, $notifiables)
    {
        Notification::send($notifiables, new OrderStatusNotification($order, $status));
    }
}
