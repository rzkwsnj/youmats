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

class Quote extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia, UnicodeJsonColumn;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('webp')
            ->nonQueued()
            ->performOnCollections(QUOTE_ATTACHMENT)->format(Manipulations::FORMAT_WEBP);
    }

    public function registerMediaCollections(): void{
        $this->addMediaCollection(QUOTE_ATTACHMENT);
    }

    public function items() {
        return $this->hasMany(QuoteItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
