<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Solution;

class SubscribedExpertAddedSolution extends Notification
{
    public $solution;

    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'solution_id' => $this->solution->id,
            'title' => $this->solution->title,
            'expert_name' => $this->solution->expert->name,
            'problem_title' => $this->solution->problem->title,
            'message' => 'A new solution has been posted by your subscribed expert.',
        ];
    }
}