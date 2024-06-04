<?php

namespace App\Http\Controllers\Front\Product;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlaced;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use zakariatlilani\payfort\Facades\Payment;

class PaymentController extends Controller
{
    public string $provider = 'payfort';

    public function form()
    {
        try {
            $data['order'] = Order::whereOrderId(Cache::get('order_id'))->firstorfail();

            $data['cart'] = Cart::instance('cart');
            $data['cartItems'] = $data['cart']->search(function ($cartItems) {
                return true;
            });

            return view('front.payment.form')->with($data);
        } catch (\Exception $exception) {
            return redirect()->route('home');
        }
    }

    public function submit()
    {
        $merchant_reference = rand(0, getrandmax());
        return Payment::use($this->provider, $merchant_reference)->pay();
    }

    public function success()
    {
        try {
            $data['order'] = Order::whereOrderId(Cache::get('order_id'))->firstorfail();

            $data['cart'] = Cart::instance('cart');
            $data['cartItems'] = $data['cart']->search(function ($cartItems) {
                return true;
            });

            $data['order']->update([
                'reference_number' => request('merchant_reference'),
                'card_number' => request('card_number'),
                'card_type' => request('payment_option'),
                'card_name' => request('card_holder_name'),
                'card_exp_date' => request('expiry_date'),
                'transaction_date' => now(),
                'payment_status' => 'completed'
            ]);

            $data['delivery'] = cart_delivery();

            // Clear the cart!
            Cart::instance('cart')->destroy();
            // Clear Cache
            Cache::forget('order_id');

            //            Mail::to(Auth::guard('web')->user())->send(new OrderPlaced($data['order']));

            return view('front.payment.success')->with($data);
        } catch (\Exception $exception) {
            return Redirect::route('vendor.subscribe.success')->with(['request' => request()->all()]);
        }
    }

    public function error()
    {
        try {
            $data['order'] = Order::whereOrderId(Cache::get('order_id'))->firstorfail();

            $data['cart'] = Cart::instance('cart');
            $data['cartItems'] = $data['cart']->search(function ($cartItems) {
                return true;
            });

            // Clear Cache
            Cache::forget('order_id');

            return view('front.payment.error')->with($data);
        } catch (\Exception $exception) {
            return redirect()->route('vendor.subscribe.error')->with(['request' => request()->all()]);
        }
    }
}
