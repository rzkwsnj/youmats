@extends('front.layouts.master')
@section('metaTags')
    <title>{{__('checkout.failed_transaction')}} | {{env('APP_NAME')}}</title>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>

    <link rel="stylesheet" href="{{front_url()}}/assets/css/payment.css">
@endsection
@section('content')
<div class="payment-box rtl">
    <div id="app" class="card mt-50 mb-50">
        <div class="card-title">
            {{__('checkout.failed_transaction')}}
        </div>
        <form>
            <span id="card-header">{{__('checkout.product_list')}}</span>
            @foreach($cartItems as $item)
                @if($item->model->type == 'product')
                    <div class="row row-1">
                        {{--                    <div class="col-2"><img loading="lazy" class="img-fluid" src="https://img.icons8.com/color/48/000000/mastercard-logo.png" /></div>--}}
                        <div class="col-9">
                            {{$item->name}} <b>({{$item->qty . 'x' . number_format($item->price, 2)}})</b>
                        </div>
                        <div class="col-3 d-flex justify-content-center">
                            {{__('general.sar')}} {{number_format($item->qty*$item->price, 2)}}
                        </div>
                    </div>
                @endif
            @endforeach
            <div class="row row-1">
                {{--            <div class="col-2"><img loading="lazy" class="img-fluid" src="https://img.icons8.com/color/48/000000/mastercard-logo.png" /></div>--}}
                <div class="col-9">
                    {{ __('cart.shipping') }}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar') . ' ' . cart_delivery()}}
                </div>
            </div>
            <div class="row row-1">
                {{--            <div class="col-2"><img loading="lazy" class="img-fluid" src="https://img.icons8.com/color/48/000000/mastercard-logo.png" /></div>--}}
                <div class="col-9">
                    {{ __('cart.total') }}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar')}} {{ cart_total() }}
                </div>
            </div>
            <span id="card-header">{{__('checkout.checkout_details')}}</span>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.holder_name')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ Request::get('card_holder_name') ?: '*********' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.card_number')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ Request::get('card_number') ?: '**** **** **** ****' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.amount')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar')}} {{ number_format(Request::get('amount')/100, 2) }}
                </div>
            </div>
{{--            <div class="row row-1">--}}
{{--                <div class="col-9">--}}
{{--                    {{__('checkout.status')}}--}}
{{--                </div>--}}
{{--                <div class="col-3 d-flex justify-content-center">--}}
{{--                    {{ Request::get('response_message') }}--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.merchant_reference')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ Request::get('merchant_reference') }}
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
