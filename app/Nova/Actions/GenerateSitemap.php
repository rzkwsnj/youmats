<?php

namespace App\Nova\Actions;

use App\Models\Category;
use App\Models\Product;
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

class GenerateSitemap extends Action
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
        $category = $models[0];

        $path = public_path('sitemap-' . $category->slug . '.xml');
        $path_en = public_path('sitemap-' . $category->slug . '-en.xml');

        $sitemap = SitemapGenerator::create($path)->getSitemap();
        $sitemap_en = SitemapGenerator::create($path_en)->getSitemap();

        $children_categories = Category::descendantsAndSelf($category->id);

        foreach ($children_categories as $children_category) {
            try {
                $route = route('front.category', [generatedNestedSlug(optional(optional(optional($children_category)->ancestors())->pluck('slug'))->toArray(), $children_category->slug)]);
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

        $products = Product::with([
            'category' => fn($q) => $q->with(['ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug')
            ])
            ->select('id', 'name', 'category_id', 'slug')
            ->whereIn('category_id', $children_categories->pluck('id'))->get();

        foreach ($products as $product) {
            try {
                $categories = generatedNestedSlug(optional(optional(optional($product->category)->ancestors())->pluck('slug'))->toArray(), $product->category->slug);
                $route = route('front.product', [$categories, $product->slug]);
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