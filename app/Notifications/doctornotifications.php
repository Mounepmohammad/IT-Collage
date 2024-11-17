<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class doctornotifications extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $modeltype;
    public $type;
    public function __construct( $type , $modeltype)
    {
        $this->modeltype = $modeltype;
        $this->type = $type;
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
    if($this->type == 1)
    {
        $user  = $this->modeltype['student_id'];
        $username = user::find($user)->first()->name;
        $date  = $this->modeltype['date'];

        return [

            'message' => "you have a new appointmen from student : {$username} on date :{$date}"
        ];
        return [

        ];
    }

    }
}
