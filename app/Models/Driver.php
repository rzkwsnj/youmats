<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Driver extends Authenticatable implements HasMedia
{
    use HasApiTokens, SoftDeletes, HasFactory, Notifiable, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    protected $fillable = ['country_id', 'name', 'email', 'phone', 'phone2', 'whatsapp', 'email_verified_at', 'password', 'remember_token', 'active'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_200')
            ->height(200)
            ->nonQueued()
            ->performOnCollections(DRIVER_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_200_200')
            ->crop(Manipulations::CROP_CENTER, 200, 200)
            ->nonQueued()
            ->performOnCollections(DRIVER_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(DRIVER_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('licenses')
            ->nonQueued()
            ->performOnCollections(DRIVER_ID, DRIVER_LICENSE)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(DRIVER_PHOTO)->singleFile();
        $this->addMediaCollection(DRIVER_ID);
        $this->addMediaCollection(DRIVER_LICENSE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function car(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Car::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @return mixed
     */
    public function rate() {
        return $this->trips()->avg('driver_rate');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function reviews(): \Illuminate\Support\Collection
    {
        return $this->trips()->pluck('driver_review');
    }
}
