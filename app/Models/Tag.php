<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Tag extends Model {
    use SoftDeletes, HasFactory, HasTranslations, UnicodeJsonColumn;

    protected $fillable = ['name', 'desc', 'meta_title', 'meta_desc', 'meta_keywords'];

    public $translatable = ['name', 'desc', 'meta_title', 'meta_desc', 'meta_keywords'];

    protected $appends = ['translated_name'];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->where('active', true);
    }

    /**
     * @return mixed
     */
    public function getTranslatedNameAttribute() {
        return $this->getTranslations('name')[app()->getLocale()];
    }

}
