<?php

namespace App\Notifications;

use App\Models\Solution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SolutionReadyForClient extends Notification
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
            'title' => 'Your Solution is Ready',
            'message' => "A solution is available for your reported issue: {$this->solution->title}",
            'solution_id' => $this->solution->id,
            'problem_id' => $this->solution->problem_id,
        ];
    }
}
