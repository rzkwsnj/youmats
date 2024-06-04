<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, SoftDeletes, HasFactory, Notifiable, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    //    protected $guarded = ['id'];
    protected $fillable = [
        'type', 'name', 'email', 'phone', 'phone2', 'email_verified_at', 'password', 'address', 'address2',
        'latitude', 'longitude', 'remember_token', 'active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_200')
            ->height(200)
            ->nonQueued()
            ->performOnCollections(USER_PROFILE)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_30_30')
            ->crop(Manipulations::CROP_CENTER, 30, 30)
            ->nonQueued()
            ->performOnCollections(USER_PROFILE)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_50_50')
            ->crop(Manipulations::CROP_CENTER, 50, 50)
            ->nonQueued()
            ->performOnCollections(USER_PROFILE)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_200_200')
            ->crop(Manipulations::CROP_CENTER, 200, 200)
            ->nonQueued()
            ->performOnCollections(USER_PROFILE)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_height_300')
            ->height(300)
            ->nonQueued()
            ->performOnCollections(USER_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_1350_300')
            ->crop(Manipulations::CROP_CENTER, 1350, 300)
            ->nonQueued()
            ->performOnCollections(USER_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(USER_PROFILE, USER_COVER)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('licenses')
            ->nonQueued()
            ->performOnCollections(COMPANY_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(USER_PROFILE)->singleFile();
        $this->addMediaCollection(USER_COVER)->singleFile();
        $this->addMediaCollection(COMPANY_PATH);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->with('items')->orderBy('id', 'desc');
    }

    /**
     * @return HasMany
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class)->with('items')->orderBy('id', 'desc');
    }

    /**
     * @return HasMany
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @return mixed
     */
    public function rate()
    {
        return $this->trips()->avg('user_rate');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function reviews(): \Illuminate\Support\Collection
    {
        return $this->trips()->pluck('user_review');
    }
}
