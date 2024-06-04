<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Inquire extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia, UnicodeJsonColumn;

    protected $guarded = ['id'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('webp')
            ->nonQueued()
            ->performOnCollections(INQUIRE_PATH)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void{
        $this->addMediaCollection(INQUIRE_PATH);
    }
}
