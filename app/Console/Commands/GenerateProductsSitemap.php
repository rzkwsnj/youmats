<?php

namespace App\Console\Commands;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateProductsSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:products {start} {increment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for products.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = $this->argument('start');
        $increment = $this->argument('increment');

        $sitemap = SitemapGenerator::create(public_path('sitemap_products'.$start.'.xml'))->getSitemap();
        $products = Product::skip($start)->take($increment)->get();
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
            $sitemap->add(Url::create($route_en)
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8));
        }
        $sitemap->writeToFile(public_path('sitemap_products'.$start.'.xml'));

    }
}
