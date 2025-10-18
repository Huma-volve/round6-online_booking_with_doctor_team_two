<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateDatabaseNotification implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public User $user;
    public string $title;
    public string $message;
    public string $type;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $title, string $message, string $type)
    {
        $this->user = $user;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->user->notifications_enabled){
            return;
        }
        Notification::create([
            'user_id' => $this->user->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => false
        ]);

    }
}
