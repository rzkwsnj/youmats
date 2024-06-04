<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class QuoteCreated extends Notification
{
    use Queueable;

    protected User $user;
    protected Quote $quote;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Quote $quote)
    {
        $this->user  = $user;
        $this->quote = $quote;
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return NovaNotification::make()
            ->message('A new quote has been placed - by:' . $this->user->name . '!')
            ->action('See', route('quotes', $this->quote->id))
            ->icon('emoji-happy')
            ->type('success');
    }
}
