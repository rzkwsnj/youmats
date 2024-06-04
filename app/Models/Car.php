<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Car extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, HasTranslations, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    protected $fillable = ['driver_id', 'type_id', 'name', 'model', 'license_no', 'max_load', 'price_per_kilo', 'active'];

    public $translatable = ['name'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_200')
            ->height(200)
            ->nonQueued()
            ->performOnCollections(CAR_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_200_200')
            ->crop(Manipulations::CROP_CENTER, 200, 200)
            ->nonQueued()
            ->performOnCollections(CAR_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(CAR_PHOTO)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('licenses')
            ->nonQueued()
            ->performOnCollections(CAR_LICENSE)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(CAR_PHOTO);
        $this->addMediaCollection(CAR_LICENSE);
    }

    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function type() {
        return $this->belongsTo(CarType::class);
    }
}
