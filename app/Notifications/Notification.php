<?php

namespace App\Notifications;

use Coreproc\NovaNotificationFeed\Notifications\NovaBroadcastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as ParentNotification;

class Notification extends ParentNotification
{
    use Queueable;

    protected $level = 'info';
    protected $message = '';

    public function __construct($level, $message = 'Test message') {
        $this->level = $level;
        $this->message = $message;
    }

    public function via($notifiable) {
        return [
            'database',
            'broadcast',
        ];
    }

    public function toArray($notifiable) {
        return [
            'level' => $this->level,
            'message' => $this->message,
            'url' => 'https://coreproc.com',
            'target' => '_self'
        ];
    }

    public function toBroadcast($notifiable) {
        return new NovaBroadcastMessage($this->toArray($notifiable));
    }
}
