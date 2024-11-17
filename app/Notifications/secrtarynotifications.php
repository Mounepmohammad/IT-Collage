<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\doctor;


class secrtarynotifications extends Notification
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

        $user  = $this->modeltype['student_id'];
        $username = user::find($user)->first()->name;
        $doctor  = $this->modeltype['doctor_id'];
        $doctorname = doctor::find( $doctor )->first()->name;
        return [

            'message' => "you have a new appointmen to reserve from student : {$username} to doctor dr:{$doctorname}"
        ];
    }
}
