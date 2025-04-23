<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $status;

    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'whatsapp'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Order #{$this->order->id} Status Update")
                    ->line("Your order status has been updated to: {$this->status}.")
                    ->action('View Order', url("/orders/{$this->order->id}"))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->status,
        ];
    }

    public function toWhatsapp($notifiable)
    {
        // Implement WhatsApp message sending logic here
        // This is a placeholder for WhatsApp integration
    }
}
