<?php

namespace App\Models;

use App\Helpers\Traits\DefaultImage;
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

class StaticPage extends Model implements Sortable, HasMedia
{
    use SoftDeletes, HasFactory, SortableTrait, HasTranslations, InteractsWithMedia, DefaultImage, UnicodeJsonColumn;

    public $fillable = ['title', 'desc', 'short_desc', 'meta_title', 'meta_keywords', 'meta_desc'];
    public $translatable = ['title', 'desc', 'short_desc', 'meta_title', 'meta_keywords', 'meta_desc'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('size_height_300')
            ->height(300)
            ->nonQueued()
            ->performOnCollections(PAGE_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('size_1350_300')
            ->crop(Manipulations::CROP_CENTER, 1350, 300)
            ->nonQueued()
            ->performOnCollections(PAGE_PATH)->format(Manipulations::FORMAT_WEBP);

        $this->addMediaConversion('cropper')
            ->nonQueued()
            ->performOnCollections(PAGE_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(PAGE_PATH)->singleFile();
    }
}
