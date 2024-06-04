<?php

namespace App\View\Composers;

use App\Models\Vendor;
use Illuminate\View\View;

class FeaturedVendorsComposer
{
    public function compose(View $view) {
        $data['featuredVendors'] = Vendor::with(['media'])->where('isFeatured', true)->get(['id', 'name', 'slug']);

        $view->with($data);
    }
}
