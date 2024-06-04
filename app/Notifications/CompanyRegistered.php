<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class CompanyRegistered extends Notification
{
    use Queueable;

    private User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return NovaNotification::make()
            ->message('A new company was registered. - ' . $this->user->name . '!')
            ->action('See',  route('vendors', $this->user->slug))
            ->icon('emoji-happy')
            ->type('success');
    }
}
