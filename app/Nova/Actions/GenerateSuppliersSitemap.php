<?php

namespace App\Nova\Actions;

use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

ini_set('max_execution_time', 600);

class GenerateSuppliersSitemap extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {

        $path = public_path('Suppliers.xml');
        $path_en = public_path('Suppliers-en.xml');

        $sitemap = SitemapGenerator::create($path)->getSitemap();
        $sitemap_en = SitemapGenerator::create($path_en)->getSitemap();

        $vendors = Vendor::with([ 'branches' => fn($q) => $q->with([ 'city' => fn($q) => $q->select('id', 'name')
                    ])->select('id', 'city_id', 'name', 'latitude', 'longitude', 'phone_number', 'fax', 'website', 'address')])->get();

        foreach ($vendors as $vendor) {
            try {
                $route = route('vendor.show', [$vendor->slug]);
            } catch (\Exception $e) {
                continue;
            }
            $sitemap->add(Url::create($route)
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8));
            $route_en = str_replace('https://www.youmats.com/', 'https://www.youmats.com/en/', $route);
            $sitemap_en->add(Url::create($route_en)
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.8));
        }

        $sitemap->writeToFile($path);
        $sitemap_en->writeToFile($path_en);

    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
