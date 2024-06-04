<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    public function creating(Product $product) {
        if(is_null($product->slug)) {
            $product->slug = Str::slug($product->getTranslation('name', 'en')) . '-' . rand(100, 999);
        }
    }
}
