<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CarType extends Model
{
    use HasFactory, HasTranslations, UnicodeJsonColumn;

    public $translatable = ['name'];
}
