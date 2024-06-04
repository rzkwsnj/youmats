<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Znck\Eloquent\Traits\BelongsToThrough;

class Vendor extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use SoftDeletes, HasFactory, Notifiable, InteractsWithMedia, DefaultImage, HasTranslations, CascadeSoftDeletes, BelongsToThrough, UnicodeJsonColumn;

    protected $fillable = [
        'name', 'country_id', 'subCategory_id', 'email', 'phone', 'contacts', 'address', 'type', 'latitude', 'longitude',
        'password', 'facebook_url', 'twitter_url', 'pinterest_url', 'instagram_url', 'youtube_url', 'website_url', 'slug', 'active'
    ];

    protected $guard = 'vendor';

    protected $translatable = ['name', 'meta_title', 'meta_keywords', 'meta_desc'];


    protected $cascadeDeletes = ['products', 'branches'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'contacts'          => 'array',
        'location'          => 'array',
        'deleted_at'        => 'datetime'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_50')
            ->height(50)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_262_50')
            ->crop(Manipulations::CROP_CENTER, 262, 50)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_30_30')
            ->crop(Manipulations::CROP_CENTER, 30, 30)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_50_50')
            ->crop(Manipulations::CROP_CENTER, 50, 50)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_100')
            ->height(100)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_100_100')
            ->crop(Manipulations::CROP_CENTER, 100, 100)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_150')
            ->height(150)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_150_150')
            ->crop(Manipulations::CROP_CENTER, 150, 150)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_200')
            ->height(200)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_200_200')
            ->crop(Manipulations::CROP_CENTER, 200, 200)
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_300')
            ->height(300)
            ->nonQueued()
            ->performOnCollections(VENDOR_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_1350_300')
            ->crop(Manipulations::CROP_CENTER, 1350, 300)
            ->nonQueued()
            ->performOnCollections(VENDOR_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(VENDOR_LOGO, VENDOR_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('licenses')
            ->nonQueued()
            ->performOnCollections(VENDOR_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(VENDOR_LOGO)->singleFile();
        $this->addMediaCollection(VENDOR_COVER)->singleFile();
        $this->addMediaCollection(VENDOR_PATH);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, 'vendor.password.reset', 'vendors'));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification('vendor.verification.verify'));
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subscribes()
    {
        return $this->hasMany(Subscribe::class)->orderByDesc('expiry_date');
    }

    public function current_subscribes()
    {
        return $this->hasMany(Subscribe::class)->whereDate('expiry_date', '>=', now());
    }

    public function getSubscribeAttribute()
    {
        if (count($this->current_subscribes)) {
            return 1;
        }
        return 0;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(
            City::class,
            Country::class,
            'id',
            'country_id',
            'country_id',
            'id'
        );
    }

    /**
     * @return HasMany
     */
    public function shippings(): HasMany
    {
        return $this->hasMany(Shipping::class);
    }

    /**
     * @return Builder
     */
    public function categories(): Builder
    {
        return $this->belongsToMany(Category::class, Product::class)
            ->distinct()->get()->unique()->toQuery();
    }

    /**
     * @return HasMany
     */
    public function branches(): HasMany
    {
        return $this->hasMany(VendorBranch::class)->with('city');
    }

    /**
     * @return HasMany
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id', 'desc');
    }

    /**
     * @return HasMany
     */
    public function quote_items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('id', 'desc');
    }

    public function whatsapp_message(): string
    {
        $integration_number = nova_get_setting('whatsapp_manage_by_admin');
        $message = route('vendor.show', [$this->slug]);
        if (!$this->manage_by_admin) {

            if (nova_get_setting('enable_encryption_mode') || $this->enable_encryption_mode) {
                $integration_number = nova_get_setting('whatsapp_integration');
                $phone_code = ';;' . get_contact($this, 'phone_code') . ';;';
                $vendor_code = ';;' . vendor_encrypt($this) . ';;';
                $message .= '%0A,%0A' . $phone_code;
                $message .= '%0A,%0A' . $vendor_code;
            } else {
                $integration_number = (get_contact($this, 'phone')) ?? nova_get_setting('whatsapp_manage_by_admin');
            }
        }

        return 'https://wa.me/' . $integration_number . '?text=' . $message;
    }
}
