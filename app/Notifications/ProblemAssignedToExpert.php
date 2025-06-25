<?php

namespace App\Notifications;

use App\Models\ClientProblem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProblemAssignedToExpert extends Notification
{
    use Queueable;

    public function __construct(public ClientProblem $problem) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'You Have Been Assigned a New Problem',
            'message' => "Problem: {$this->problem->title} (Car ID: {$this->problem->car_id})",
            'problem_id' => $this->problem->id,
        ];
    }
}
