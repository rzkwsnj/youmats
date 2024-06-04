@extends('front.layouts.master')
@section('metaTags')
    <title>{{__('checkout.success_transaction')}} | {{env('APP_NAME')}}</title>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{front_url()}}/assets/css/payment.css">
@endsection
@section('content')
<div class="payment-box rtl">
    <div id="app" class="card mt-50 mb-50">
        <div class="card-title">
            {{__('checkout.success_transaction')}}
        </div>
        <form> <span id="card-header">{{__('checkout.product_list')}}</span>
            <div class="row row-1">
                <div class="col-9">
                    {{$membership->name . ' - ' . $category->name}} <b>({{__('general.sar')}} {{$membership->price}})</b>
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar')}} {{$membership->price}}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{ __('cart.total') }}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar')}} {{round($membership->price)}}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.holder_name')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ isset($request['customer_name']) ? $request['customer_name'] : '' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.card_number')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ isset($request['card_number']) ? $request['card_number'] : '' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.amount')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{__('general.sar')}} {{ isset($request['amount']) ? $request['amount']/100 : '' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.status')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ isset($request['response_message']) ? $request['response_message'] : '' }}
                </div>
            </div>
            <div class="row row-1">
                <div class="col-9">
                    {{__('checkout.merchant_reference')}}
                </div>
                <div class="col-3 d-flex justify-content-center">
                    {{ isset($request['merchant_reference']) ? $request['merchant_reference'] : '' }}
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
