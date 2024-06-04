<?php


namespace App\Http\Controllers\Front\Product;


use App\Http\Controllers\Controller;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        Cart::instance('wishlist')
            ->restore(Auth::user()->email);

        $wishProducts = Cart::instance('wishlist')->content();

        return view('front.wishlist.index', ['wishProducts' => $wishProducts]);
    }

    /**
     * @param $product
     * @return JsonResponse
     */
    public function add(Product $product): JsonResponse
    {
        Cart::instance('wishlist')
            ->restore(Auth::user()->email);

        $items = Cart::instance('wishlist')->content();

        foreach($items as $item) {
            if($item->id == $product->id)
                return response()->json(['status' => false, 'message' => __('This product has been already added to your wishlist')]);
        }

        Cart::instance('wishlist')
            ->add($product->id, $product->name, 1, $product->price)
            ->associate($product);

        Cart::instance('wishlist')
            ->store(Auth::user()->email);

        return response()->json(['status' => true, 'message' => __('Product has been added to your wishlist')]);
    }

    /**
     * @param $rowId
     * @return JsonResponse
     */
    public function deleteItem($rowId): JsonResponse
    {
        Cart::instance('wishlist')
            ->restore(Auth::user()->email);

        Cart::instance('wishlist')
            ->remove($rowId);

        Cart::instance('wishlist')
            ->store(Auth::user()->email);

        return response()->json(['status' => true, 'message' => __('Product has been deleted from your wishlist'), 'count' => Cart::instance('wishlist')->count()]);
    }
}
