<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class usernotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     public $modeltype;
    public function __construct($modeltype)
    {
        $this->modeltype = $modeltype;
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


    public function toArray(object $notifiable): array
    {
        return [
            
        ];
    }
}
