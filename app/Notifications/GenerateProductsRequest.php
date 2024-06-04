<?php

namespace App\Notifications;

use App\Models\GenerateProduct;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaNotification;

class GenerateProductsRequest extends Notification
{
    use Queueable;

    protected Vendor $vendor;
    protected GenerateProduct $generateProducts;


    public function __construct(Vendor $vendor, GenerateProduct $generateProducts)
    {
        $this->vendor = $vendor;
        $this->generateProducts = $generateProducts;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
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
            ->message('There is a new products generate request in the system - by:' . $this->vendor->name . '!')
            ->action('See', route('generate-products', $this->generateProducts->id))
            ->icon('emoji-happy')
            ->type('success');
    }
}
