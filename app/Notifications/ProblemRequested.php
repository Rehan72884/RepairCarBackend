<?php

namespace App\Notifications;

use App\Models\Problem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProblemRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Problem $problem) {}

    public function via($notifiable)
    {
        return ['database']; // or ['mail', 'database']
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'A client has requested a solution for a car problem: ' . $this->problem->title,
            'car_id' => $this->problem->car_id,
            'client_id' => $this->problem->client_id,
        ];
    }
}

