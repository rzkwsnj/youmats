<?php

namespace App\View\Composers;

use App\Models\Partner;
use Illuminate\View\View;

class FeaturedPartnersComposer
{
    public function compose(View $view) {
        $data['featuredPartners'] = Partner::with(['media'])->where('featured', true)->get(['id', 'name', 'link']);

        $view->with($data);
    }
}
