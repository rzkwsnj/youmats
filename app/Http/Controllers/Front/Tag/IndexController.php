<?php

namespace App\Http\Controllers\Front\Tag;

use App\Helpers\Classes\CollectionPaginate;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    private function getTags($tags_ids)
    {
        return Tag::whereIn('id', $tags_ids)->get();
    }

    public function tag()
    {
        $data['tags'] = Tag::select('id', 'name', 'slug')->get();

        return view('front.tag.tag')->with($data);
    }

    public function index($tag_slug)
    {
        $data['tag'] = Tag::with([
            'products' => fn ($q) => $q->with([
                'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                    ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status'),
                'media',
                'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats')
            ])->inRandomOrder()
        ])->where('slug', $tag_slug)->firstOrFail();

        $products = $data['tag']->getRelation('products');

        $data['products'] = CollectionPaginate::paginate($products, 20);
        $data['products']->withPath(url()->current())->withQueryString();

        $tags_ids = [];
        foreach ($data['products'] as $product) {
            foreach ($product->tags as $tag) {
                $tags_ids[] = $tag->id;
            }
        }

        $data['tags'] = $this->getTags($tags_ids);

        return view('front.tag.index')->with($data);
    }

    public function shop()
    {
        $keywords = [];
        $products = Product::where('active', true)
            ->whereNotNull('search_keywords')
            ->select('search_keywords')->get();

        foreach ($products as $product) {
            foreach (explode('\r\n', trim(json_encode($product->search_keywords, JSON_UNESCAPED_UNICODE), '"')) as $keyword) {
                if (!empty($keyword))
                    $keywords[str_replace(' ', '-', $keyword)] = $keyword;
            }
        }

        $data['keywords'] = CollectionPaginate::paginate(collect($keywords), 100);
        $data['keywords']->withPath(url()->current())->withQueryString();

        return view('front.tag.shop')->with($data);
    }

    /**
     * @param $search_keyword
     * @return Application|Factory|View
     */
    public function searchKeywordsTags($search_keyword)
    {
        //abort_if(str_contains($search_keyword, ' '), 404);
        $search_keyword = Str::replace('-', ' ', $search_keyword);

        $data['products'] = Product::with([
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status'),
            'media',
            'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats')
        ])->where('active', true)
            ->where("search_keywords->" . app()->getLocale(), "LIKE", "%$search_keyword%")
            ->inRandomOrder()->paginate(20);

        $data['keyword'] = $search_keyword;

        abort_if(!count($data['products']), 404);

        return view('front.tag.search_keywords_tags')->with($data);
    }
}
