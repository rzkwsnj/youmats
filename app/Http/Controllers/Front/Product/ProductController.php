<?php

namespace App\Http\Controllers\Front\Product;

use App\Helpers\Classes\AttributeFilter;
use App\Helpers\Classes\CollectionPaginate;
use App\Helpers\Classes\DeliveryFilter;
use App\Helpers\Classes\PriceFilter;
use App\Helpers\Classes\ProductsSortDelivery;
use App\Helpers\Filters\FiltersJsonField;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Category\CategoryController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Stmt\Else_;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{

    public function index($categories_slug, $slug)
    {
        $data['product'] = Product::with([
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->with([
                'ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')
            ])->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
            'media',
            'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats'),
            'tags'
        ])->where(['slug' => $slug, 'active' => true])->first();

        $this->checkProductSubSlugs($data['product'], $categories_slug);

        if (Session::has('city')) {
            $data['delivery'] = $data['product']->delivery;
            $data['delivery_cities'] = $data['product']->delivery_cities();
        }

        $data['product']->views++;
        $data['product']->save();

        if ($data['product']->subscribe) {
            $data['same_vendor_products'] = Product::with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats'),
            ])->where('category_id', $data['product']->category_id)
                ->where('vendor_id', $data['product']->vendor_id)
                ->where('id', '!=', $data['product']->id)
                ->where('active', true)
                ->inRandomOrder()->take(10)->get();
        }

        foreach ($data['product']->category->ancestorsAndSelf($data['product']->category->id) as $ancestor) {
            $data['ancestor_array'][] = $ancestor->show_warning;
        }

        foreach ($data['product']->vendor->contacts as $contact) {
            if ((Session::get('userType') == $contact['with'] or $contact['with'] == 'both')) {
                $data['contact'] = $contact;
            }
        }

        if (!isset($data['contact']['with'])) {
            $data['contact']['with'] = "";
        }

        $category_attributes = Attribute::where(['category_id' => $data['product']->category->id])->get();
        if (count($category_attributes)) {
            for ($i = 0; $i < count($category_attributes); $i++) {
                $attributes_order[$i] = AttributeValue::where(['attribute_id' => $category_attributes[$i]->id])->pluck('id')->toArray();
                $attributes = $data['product']->attributes(['attribute_id' => $category_attributes[$i]->id], ['product_id' => $data['product']->id])->pluck('attribute_values_products.attribute_id')->toArray();
            }
        }

        if (isset($attributes)) {

            for ($i = 0; $i < count($attributes); $i++) {
                for ($z = 0; $z < count($attributes_order); $z++) {
                    if (in_array($attributes[$i], $attributes_order[$z])) {

                        $ordered_attributes[$attributes[$i]] = $z;
                        break;
                    }
                }
            }

            if (isset($ordered_attributes)) {
                asort($ordered_attributes);
                $ordered_attributes = array_keys($ordered_attributes);
            } else {
                $ordered_attributes = array();
            }

            $attributes = array_reverse($ordered_attributes);

            for ($z = 1; $z < count($attributes); $z++) {

                $spesific_attribute = $attributes[$z];
                $data['same_attribute_products'] = Product::with([
                    'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                        ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
                    'media',
                    'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats'),
                ])
                    ->where('vendor_id', '!=', $data['product']->vendor_id)
                    ->where('category_id', $data['product']->category->id)
                    ->where('id', '!=', $data['product']->id)
                    ->where('active', true)
                    ->whereHas('attributes', function ($query) use ($spesific_attribute) {
                        $query->Where(function ($query) use ($spesific_attribute) {
                            $query->orWhere('attribute_values_products.attribute_id', '=',  $spesific_attribute);
                        });
                    })
                    ->inRandomOrder()
                    ->limit(500)
                    ->get()
                    ->sortByDesc('contacts')->groupBy('contacts')->map(function (Collection $collection) {
                        return $this->SortRelated($collection);
                    })->ungroup()
                    ->sortByDesc('subscribe')->groupBy('subscribe')->map(function (Collection $collection) {
                        return $collection->sortByDesc('contacts')->groupBy('contacts')->map(function (Collection $collection) {
                            return $this->customSort($collection);
                        })->ungroup();
                    })->ungroup();

                if (count($data['same_attribute_products']) > 0) {
                    break;
                }
            }
        }

        $data['subscribed_vendors'] = Vendor::with(['products' => function ($query) use ($data) {
            $query->with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats'),
            ])->where('category_id', $data['product']->category_id)->inRandomOrder();
        }])
            ->join('products', 'products.vendor_id', '=', 'vendors.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('subscribes', 'subscribes.vendor_id', '=', 'vendors.id')
            ->whereDate('subscribes.expiry_date', '>', now())
            ->where('subscribes.category_id', $data['product']->category_id)
            ->where('products.category_id', $data['product']->category_id)
            ->where('vendors.active', true)
            ->where('vendors.id', '!=', $data['product']->vendor_id)
            ->select('vendors.*')
            ->inRandomOrder()->distinct()->get();

        $data['same_category_products'] = Product::with([
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
            'media',
            'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats'),
        ])->where('category_id', $data['product']->category_id)
            ->where('id', '!=', $data['product']->id)
            ->where('active', true)
            ->inRandomOrder()->take(10)->get();

        if ($data['product']->subscribe && !$data['product']->vendor->manage_by_admin) {
            $data['widget_phone'] = Clean_Phone_Number(get_contact($data['product']->vendor, 'call_phone'));
            $data['widget_whatsapp'] = $data['product']->whatsapp_message();
        }

        if (is_company()) {
            foreach ($data['product']->vendor->contacts as $contact) {
                if ((Session::get('userType') == $contact['with'] or $contact['with'] == 'both')) {
                    $data['contact'] = $contact;
                    break;
                }
            }
        }

        return view('front.product.index')->with($data);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function all(Request $request)
    {
        if (isset($request->filter['city'])) {
            setCity($request->filter['city']);
        }

        $data['products'] = QueryBuilder::for(Product::class)
            ->with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin')
            ])
            ->where('active', true)->orderBy('updated_at', 'desc')->paginate(20);


        $data['products']->withPath(url()->current())->withQueryString();

        return view('front.product.all')->with($data);
    }

    /**
     * @return string
     */
    public function suggest(): string
    {
        $data['suggested_products'] = QueryBuilder::for(Product::class)
            ->select('id', 'category_id', 'name', 'slug')
            ->allowedFilters([AllowedFilter::custom('name', new FiltersJsonField)])
            ->where('active', true)
            ->orderBy('updated_at', 'desc')->limit(5)->get();

        return view('front.layouts.partials.searchDiv')->with($data)->render();
    }

    public function search($search_word)
    {

        $search_word = str_replace("-", " ", $search_word);
        $data['page_title'] = $search_word ?? '';
        $data['selected_tags'] = [];
        $data['selected_categories'] = [];

        $request = Request();

        if (isset($request->filter['city'])) {
            setCity($request->filter['city']);
        }

        $products = QueryBuilder::for(Product::class)
            ->with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status', 'show_warning'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin'),
                'tags' => fn ($q) => $q->select('tags.id', 'name', 'slug')
            ])
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
            )->allowedFilters([
                AllowedFilter::custom('name', new FiltersJsonField)->default($search_word),
                AllowedFilter::scope('price'),
                AllowedFilter::custom('is_price', new PriceFilter()),
                AllowedFilter::custom('is_delivery', new DeliveryFilter())
            ]);

        $data['minPrice'] = $products->min('price');
        $data['maxPrice'] = $products->max('price');


        if (isset($request->sort) && is_individual()) {
            $filter = $products->allowedSorts([
                'price',
                AllowedSort::custom('delivery', new ProductsSortDelivery($products), 'delivery')
            ])->take(200)->get()->unique();
        } else {
            $filter = $products->take(200)->get()
                ->sortByDesc('subscribe')->groupBy('subscribe')->map(function (Collection $collection) {
                    return $collection->sortByDesc('contacts')->groupBy('contacts')->map(function (Collection $collection) {
                        return (new CategoryController())->customSort($collection);
                    })->ungroup();
                })->ungroup()
                ->unique();
        }

        $data['search_products'] = CollectionPaginate::paginate($filter, 20);
        $data['search_products']->withPath(url()->current())->withQueryString();

        foreach ($filter as $product) {
            if ($product->tags)
                foreach ($product->tags as $tag) {
                    $data['search_tags'][$tag->id] = $tag;
                }
            if (isset($product->category->id))
                $data['search_categories'][$product->category->id] = $product->category;
        }

        return view('front.search.search')->with($data);
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

    public function SortRelated($groupCollection)
    {

        foreach ($groupCollection as $product) {
            if (isset($product) && $product->subscribe && Has_Delivery($product->vendor->contacts) && Has_Client_Type($product->vendor->contacts)) {
                $newCollection[] = $product;
            }
        }

        if (isset($newCollection)) {
            return $newCollection;
        } else {
            return $groupCollection;
        }
    }

    private function checkProductSubSlugs($product, $categories_slug)
    {

        if (!is_null($product)) {
            $correct_link = route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug]);
            if ($correct_link != url()->current()) {
                redirect()->to($correct_link, 301)->send();
            }
        } else {
            $categories_slug_array = array_reverse(explode('/', $categories_slug));
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
}
