<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class ProductCreated extends Notification
{
    use Queueable;

    protected Vendor $vendor;
    protected Product $product;

    /**
     * ProductCreated constructor.
     * @param Vendor $vendor
     * @param Product $product
     */
    public function __construct(Vendor $vendor, Product $product)
    {
        $this->vendor = $vendor;
        $this->product = $product;
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
            ->message('There is a new product in the system - by:' . $this->vendor->name . '!')
            ->action('See', route('products', $this->product->id))
            ->icon('emoji-happy')
            ->type('success');
    }
}
