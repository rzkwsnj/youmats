<?php

namespace App\Models;

use App\Helpers\Traits\UnicodeJsonColumn;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory, CascadeSoftDeletes, UnicodeJsonColumn;

    protected $casts = [
        'transaction_date' => 'datetime'
    ];

    protected $fillable = [];
    protected $guarded = [];

    protected $cascadeDeletes = ['items'];

    public function getTotalPriceAttribute($value)
    {
        return round($value * getCurrency('rate'), 2);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
