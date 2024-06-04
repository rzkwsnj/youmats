<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class AttributeValue extends Model
{
    use HasFactory, HasTranslations, UnicodeJsonColumn;

    public $fillable = ['value'];
    public $translatable = ['value'];

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute() {
        if(!isset($this->getTranslations('value')[app()->getLocale()]))
            return $this->value;
        return $this->getTranslations('value')[app()->getLocale()] . ' (' . $this->attribute->key . ')';
    }

    /**
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'attribute_values_products', 'attribute_id', 'product_id');
    }
}
