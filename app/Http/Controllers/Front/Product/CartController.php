<?php

namespace App\Http\Controllers\Front\Product;

use App\Helpers\Classes\Shipping;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // todo: optimize this class

    /**
     * @return Application|Factory|View
     */
    public function show()
    {
        $data['items'] = Cart::instance('cart')->content();

        return view('front.cart.index')->with($data);
    }

    public function add(Request $request, Product $product)
    {
        $min_quantity = $product->min_quantity;
        $stock = $product->stock;

        $cart = Cart::instance('cart')->content();

        foreach ($cart as $item) {
            if($item->id == $product->id) {

                $quantity = $request->quantity + $item->qty;

                if(!isset($quantity))
                    $quantity = $min_quantity ?? 1;

                if($quantity < $min_quantity)
                    return response()->json([
                        'message' => __('messages.min_order_quantity_constrain'),
                        'success' => false
                    ]);

                if((!is_company()) && $quantity > $stock)
                    return response()->json([
                        'message' => __('messages.out_of_stock'),
                        'success' => false
                    ]);

//                if(!Shipping::abilityOfQuantity($product, $quantity))
//                    return response()->json([
//                        'message' => __('messages.not_ability_quantity'),
//                        'success' => false
//                    ]);

                Cart::instance('cart')->add(
                    $product->id,
                    $product->name,
                    $request->quantity,
                    round($product->price / getCurrency('rate'), 2),
                    [],
                    0
                )->associate($product);

                return response()->json([
                    'message' => __(is_company() ? 'product.added_to_quote_list' : 'product.added_to_cart'),
                    'success' => true,
                    'cart' => Cart::instance('cart')->content(),
                    'total' => getCurrency('code') . ' ' . cart_total(),
                    'count' => Cart::instance('cart')->count()
                ]);

            }
        }

        $quantity = $request->quantity;

        if(!isset($quantity))
            $quantity = $min_quantity ?? 1;

        if($quantity < $min_quantity)
            return response()->json([
                'message' => __('messages.min_order_quantity_constrain'),
                'success' => false
            ]);

        if((!is_company()) && $quantity > $stock)
            return response()->json([
                'message' => __('messages.out_of_stock'),
                'success' => false
            ]);

//        if(!Shipping::abilityOfQuantity($product, $quantity))
//            return response()->json([
//                'message' => __('messages.not_ability_quantity'),
//                'success' => false
//            ]);

        Cart::instance('cart')->add(
            $product->id,
            $product->name,
            $quantity,
            round($product->price / getCurrency('rate'), 2),
            [],
            0
        )->associate($product);

        return response()->json([
            'message' => __(is_company() ? 'product.added_to_quote_list' : 'product.added_to_cart'),
            'success' => true,
            'cart' => Cart::instance('cart')->content(),
            'total' => getCurrency('code') . ' ' . cart_total(),
            'count' => Cart::instance('cart')->count()
        ]);
    }

    public function delivery_warning(Request $request, Product $product) {
        $cart = Cart::instance('cart')->content();
        $quantity = $request->quantity;

        foreach ($cart as $item) {
            if ($item->id == $product->id) {
                $quantity = $request->quantity + $item->qty;
            }
        }

        return response()->json(Shipping::abilityOfQuantity($product, $quantity));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $product = Product::findorfail($request->id);

        $min_quantity = $product->min_quantity;
        $stock = $product->stock;

        $cart = Cart::instance('cart')->content();

        foreach ($cart as $item) {
            if($item->id == $product->id) {

                $quantity = $request->quantity + $item->qty;

                if(!isset($quantity))
                    $quantity = $min_quantity ?? 1;

                if($quantity < $min_quantity)
                    return response()->json([
                        'message' => __('messages.min_order_quantity_constrain'),
                        'success' => false
                    ]);

                if((!is_company()) && $quantity > $stock)
                    return response()->json([
                        'message' => __('messages.out_of_stock'),
                        'success' => false
                    ]);

//                if(!Shipping::abilityOfQuantity($product, $quantity))
//                    return response()->json([
//                        'message' => __('messages.not_ability_quantity'),
//                        'success' => false
//                    ]);

                Cart::instance('cart')->update($request->rowId, $quantity);

                return response()->json(['status' => true]);
            }
        }

        $request_quantity = $request->qty;

        if($request_quantity < $min_quantity)
            return response()->json(['status' => false]);

        if((!is_company()) && $request_quantity > $stock)
            return response()->json(['status' => false]);

        Cart::instance('cart')->update($request->rowId, $request_quantity);

        return response()->json(['status' => true]);
    }

    /**
     * @param Request $request
     * @param $rowId
     * @return JsonResponse
     */
    public function deleteItem(Request $request, $rowId): JsonResponse
    {
        Cart::instance('cart')->remove($rowId);

        return response()->json([
            'status' => true,
            'total' => getCurrency('code') . ' ' . cart_total(),
            'count' => Cart::count(),
            'subtotal' => getCurrency('code') . ' ' .Cart::subtotal(),
            'delivery' => getCurrency('code') . ' ' .cart_delivery()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        Cart::instance('cart')->destroy();

        return response()->json(['status' => true, 'message' => __('cart.cart_has_been_destroyed')]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|max:50|string'
        ]);
        $coupon = Coupon::whereCode($request->code)
                    ->whereStatus(1)
                    ->first();

        //Coupon doesn't exist. Lets return an error!
        if(!$coupon)
            return back()->with(['custom_error' => __('cart.coupon_code_doesnt_exist') ]);

        $total = parseNumber(cart_total());

        //The total of the cart is less than the coupon starting price
        if($total < $coupon->price)
            return back()->with(['custom_error' => __('cart.coupon_code_discount_higher_than_cart_total') ]);

        //The coupon code has been added already.
        if(Cart::instance('cart')->search(function($cartItem, $rowId) {
                return $cartItem->id == 'discount';
            })->count() > 0)
            return back()->with(['custom_error' => __('cart.coupon_already_added') ]);

        //Coupon discount more than the cart total. So let's make the cart 0!
        if($coupon->value > $total)
            $discount = ($coupon->value + $total) - $coupon->value;
        else
            $discount = $coupon->value;

        //Apply the discount.
        Cart::instance('cart')
            ->add('discount', $coupon->code, 1, -$discount, [], 0);

        if($coupon->singleUse) {
            $coupon->status = 0;
            $coupon->save();
        }

        return back()->with(['custom_success' => __('cart.coupon_activated') ]);
    }
}
