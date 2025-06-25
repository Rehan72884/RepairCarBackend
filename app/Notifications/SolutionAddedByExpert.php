<?php

namespace App\Notifications;

use App\Models\Solution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolutionAddedByExpert extends Notification
{
    use Queueable;

    public function __construct(public Solution $solution) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Expert Added a New Solution',
            'message' => "Expert {$this->solution->expert->name} added a solution: {$this->solution->title}",
            'solution_id' => $this->solution->id,
            'problem_id' => $this->solution->problem_id,
        ];
    }
}