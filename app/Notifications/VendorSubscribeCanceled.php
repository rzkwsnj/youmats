<?php

namespace App\Notifications;

use App\Models\Subscribe;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class VendorSubscribeCanceled extends Notification
{
    use Queueable;

    private Vendor $vendor;
    private Subscribe $subscribe;

    /**
     * VendorSubscribeCanceled constructor.
     * @param Vendor $vendor
     * @param Subscribe $subscribe
     */
    public function __construct(Vendor $vendor, Subscribe $subscribe)
    {
        $this->vendor = $vendor;
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
            ->message('Vendor: ' . $this->vendor->name . ' canceled his subscription!')
            ->action('See', route('subscribes', $this->subscribe->id))
            ->icon('shield-exclamation')
            ->type('error');
    }
}
