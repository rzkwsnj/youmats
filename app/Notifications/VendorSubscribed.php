<?php

namespace App\Notifications;

use App\Models\Category;
use App\Models\Membership;
use App\Models\Subscribe;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class VendorSubscribed extends Notification
{
    use Queueable;

    private Vendor $vendor;
    private Membership $membership;
    private Category $category;
    private Subscribe $subscribe;

    /**
     * VendorSubscribed constructor.
     * @param Vendor $vendor
     * @param Membership $membership
     * @param Category $category
     * @param Subscribe $subscribe
     */
    public function __construct(Vendor $vendor, Membership $membership, Category $category, Subscribe $subscribe)
    {
        $this->vendor = $vendor;
        $this->membership = $membership;
        $this->category = $category;
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
            ->message('Vendor: ' . $this->vendor->name . ' Subscribed to an ' . $this->membership->name . ' plan in category: ' . $this->category->name . '.')
            ->action('See', route('subscribes', $this->subscribe->id))
            ->icon('emoji-happy')
            ->type('success');
    }
}
