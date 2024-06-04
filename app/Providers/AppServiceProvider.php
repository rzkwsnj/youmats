<?php

namespace App\Providers;

use App\Models\StaticImage;
use App\View\Composers\FeaturedPartnersComposer;
use App\View\Composers\FeaturedVendorsComposer;
use App\View\Composers\GeneralComposer;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register() {
        Schema::defaultStringLength(191);
    }

    public function boot() {
        Paginator::useBootstrap();
        try {
            set_user_type();
            setCityUsingLocation();

            $data['staticImages'] = StaticImage::first();
            View::share($data);

            view()->composer('front.layouts.master', GeneralComposer::class);
            view()->composer('front.layouts.partials.vendors', FeaturedVendorsComposer::class);
            view()->composer('front.layouts.partials.partners', FeaturedPartnersComposer::class);
        } catch (Exception $exception) {}
    }
}
