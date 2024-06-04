<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Language extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, UnicodeJsonColumn;

    public $translatable = ['name'];

    public $fillable = ['name'];

    protected $casts = [
        'image' => 'array',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_20')
            ->height(20)
            ->nonQueued()
            ->performOnCollections(LANGUAGE_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_20_20')
            ->crop(Manipulations::CROP_CENTER, 20, 20)
            ->nonQueued()
            ->performOnCollections(LANGUAGE_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(LANGUAGE_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(LANGUAGE_PATH)->singleFile();
    }
}
