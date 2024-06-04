<?php

namespace App\Http\Controllers\Front\Category;

use App\Helpers\Classes\AttributeFilter;
use App\Helpers\Classes\CollectionPaginate;
use App\Helpers\Classes\DeliveryFilter;
use App\Helpers\Classes\PriceFilter;
use App\Helpers\Classes\ProductsSortDelivery;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{


    public function index($slug, Request $request)
    {

        $this->checkOnCategoriesSlugs($slug);

        $parsedSlug = explode('/', ((string) $slug));
        $slug_end = end($parsedSlug);

        $data['category'] = Category::with([
            'parent' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug'),
            'children' => fn ($q) => $q->with([
                'ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
                'media'
            ])->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug'),
            'ancestors' => fn ($q) => $q->with([
                'ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
            ])->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug'),
            'descendants',
            'attributes' => fn ($q) => $q->with([
                'values' => fn ($q) => $q->select(['id', 'attribute_id', 'value'])
            ])->select('id', 'category_id', 'key')
        ])->whereSlug($slug_end)
            ->firstOrFail(['id', 'name', 'title', 'parent_id', '_lft', '_rgt', 'contact_widgets', 'slug', 'meta_title', 'meta_desc', 'meta_keywords', 'schema', 'desc']);

        $data['parent'] = $data['category']->parent;
        $data['children'] = $data['category']->children;

        $children_categories_ids = $data['category']->descendants()->pluck('id')->push($data['category']->id);

        if (isset($request->filter['city'])) {
            setCity($request->filter['city']);
        }

        $products = QueryBuilder::for(Product::class)
            ->with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats')
            ])
            ->whereIn('category_id', $children_categories_ids)
            ->where('products.active', true)
            ->select(
                'products.id',
                'products.category_id',
                'products.vendor_id',
                'products.name',
                'products.short_desc',
                'products.type',
                'products.price',
                'products.stock',
                'products.min_quantity',
                'products.active',
                'products.shipping_id',
                'products.specific_shipping',
                'products.shipping_prices',
                'products.slug',
                'products.sort'
            );

        $data['minPrice'] = $products->min('price');
        $data['maxPrice'] = $products->max('price');

        $products->allowedFilters([
            AllowedFilter::custom('attributes', new AttributeFilter()),
            AllowedFilter::scope('price'),
            AllowedFilter::custom('is_price', new PriceFilter()),
            AllowedFilter::custom('is_delivery', new DeliveryFilter())
        ]);

        if (isset($request->sort) && is_individual()) {
            $data['products'] = $products->allowedSorts([
                'price',
                AllowedSort::custom('delivery', new ProductsSortDelivery($products), 'delivery')
            ])->paginate(20);
        } else {
            $filter = $products->inRandomOrder()->take(100)->get()
                ->sortByDesc('subscribe')->groupBy('subscribe')->map(function (Collection $collection) {
                    return $collection->sortByDesc('contacts')->groupBy('contacts')->map(function (Collection $collection) {
                        return $this->customSort($collection);
                    })->ungroup();
                })->ungroup();

            $data['products'] = CollectionPaginate::paginate($filter, 20);
            $data['products']->withPath(url()->current())->withQueryString();
        }

        if (isset($data['parent'])) {
            $data['subscribeVendors'] = $data['category']->subscribedVendors();
            $data['tags'] = $data['category']->tags();
            $data['siblings'] = $data['category']->siblings()->with([
                'ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')
            ])->get(['id', 'name', 'parent_id', '_lft', '_rgt', 'slug']);

            return view('front.category.sub')->with($data);
        } else {
            return view('front.category.index')->with($data);
        }
    }

    public function customSort($collection)
    {
        $groupCollection = $collection->groupBy('vendor_id');
        $maxCount = count(max($groupCollection->toArray()));
        $groupCollection = $groupCollection->shuffle();
        foreach ($groupCollection as $key => $vendor_group) {
            $groupCollection[$key] = $vendor_group->shuffle();
        }
        for ($i = 0; $i < $maxCount; $i++) {
            foreach ($groupCollection as $vendor_group) {
                if (isset($vendor_group[$i]))
                    $newCollection[] = $vendor_group[$i];
            }
        }
        return $newCollection;
    }

    private function checkOnCategoriesSlugs($categories_slug)
    {
        $categories_slug_array = array_reverse(explode('/', ((string) $categories_slug)));
        foreach ($categories_slug_array as $key => $category_slug) {
            $category = Category::with(['ancestors' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),])->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug'),])
                ->whereSlug($category_slug)
                ->first(['id', 'name', 'title', 'parent_id', '_lft', '_rgt', 'contact_widgets', 'slug', 'meta_title', 'meta_desc', 'meta_keywords', 'schema', 'desc']);

            if (!is_null($category)) {
                $correct_link = route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)]);
                if ($correct_link == url()->current()) {
                    break;
                } else {
                    redirect()->to($correct_link, 301)->send();
                }
            } elseif ($key == count($categories_slug_array) - 1) {
                redirect()->to("/", 301)->send();
            }
        }
    }
}
