<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use SoftDeletes, HasFactory, HasTranslations, UnicodeJsonColumn;

    public $fillable = ['name'];
    public $translatable = ['name'];

    public function country() {
        return $this->belongsTo(Country::class);
    }
}
