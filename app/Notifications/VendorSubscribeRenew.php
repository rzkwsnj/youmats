<?php

namespace App\Notifications;

use App\Models\Subscribe;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class VendorSubscribeRenew extends Notification
{
    use Queueable;

    /**
     * @var Subscribe
     */
    private Subscribe $subscribe;


    /**
     * VendorSubscribeRenew constructor.
     * @param Subscribe $subscribe
     */
    public function __construct(Subscribe $subscribe)
    {
        $this->subscribe = $subscribe;
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
            ->message('Vendor: ' . $this->subscribe->vendor->name . ' renewed his subscription.')
            ->action('See', route('subscribes', $this->subscribe->id))
            ->icon('emoji-happy')
            ->type('success');
    }
}
