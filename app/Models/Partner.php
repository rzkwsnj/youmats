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

class Partner extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia, DefaultImage, HasTranslations, UnicodeJsonColumn;

    protected $fillable = ['name'];

    protected $translatable = ['name'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('size_height_50')
            ->height(50)
            ->nonQueued()
            ->performOnCollections(PARTNER_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_262_50')
            ->crop(Manipulations::CROP_CENTER, 262, 50)
            ->nonQueued()
            ->performOnCollections(PARTNER_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(PARTNER_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection(PARTNER_PATH)->singleFile();
    }

}
