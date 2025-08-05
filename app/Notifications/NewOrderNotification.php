<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $message, $title, $orderId;
    /**
     * Create a new notification instance.
     */
    public function __construct($message, $title, $orderId)
    {
        $this->message = $message;
        $this->title = $title;
        $this->orderId = $orderId;
    }
    

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'title' => $this->title,
            'order_id' => $this->orderId,
            'user_id' => $notifiable->id,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'title' => $this->title,
            'order_id' => $this->orderId,
            'user_id' => $notifiable->id,
        ];
    }
}
