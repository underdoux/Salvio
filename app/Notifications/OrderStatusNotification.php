<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Twilio\Rest\Client;

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
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');
        $to = 'whatsapp:' . $notifiable->phone_number;

        $client = new Client($sid, $token);

        $message = "Order #{$this->order->id} status has been updated to: {$this->status}.";

        try {
            $client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            // Log or handle the error as needed
            \Log::error("Failed to send WhatsApp message: " . $e->getMessage());
        }
    }
}
