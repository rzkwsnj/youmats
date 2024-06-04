<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model implements Sortable
{
    use HasFactory, SortableTrait, HasTranslations, UnicodeJsonColumn;

    public $fillable = ['key'];
    public $translatable = ['key'];

    public function Category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function values() {
        return $this->hasMany(AttributeValue::class, 'attribute_id')
                    ->orderByRaw("CAST(REPLACE(json_extract(`value`, '$.ar'), '\"', '') AS int) asc");
    }
}
