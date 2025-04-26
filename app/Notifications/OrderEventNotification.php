<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Twilio\Rest\Client;

class OrderEventNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $event;
    protected $message;

    public function __construct($order, $event, $message)
    {
        $this->order = $order;
        $this->event = $event;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'whatsapp'];
    }

    public function toMail($notifiable)
    {
        // Use configurable email templates here
        return (new MailMessage)
                    ->subject("Order #{$this->order->id} - {$this->event}")
                    ->line($this->message)
                    ->action('View Order', url("/orders/{$this->order->id}"))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'event' => $this->event,
            'message' => $this->message,
        ];
    }

    public function toWhatsapp($notifiable)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');
        $to = 'whatsapp:' . $notifiable->phone_number;

        $client = new Client($sid, $token);

        try {
            $client->messages->create($to, [
                'from' => $from,
                'body' => $this->message,
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to send WhatsApp message: " . $e->getMessage());
        }
    }
}
