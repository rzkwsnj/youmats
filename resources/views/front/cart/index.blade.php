@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Cart</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:title" content="" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@YouMats">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{ route('home') }}">{{ __('general.home') }}</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{ __(is_company() ? 'cart.quote_items' : 'cart.cart') }}</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>

    <div class="container">
        @if(count($items) > 0)
            <div class="row">
                <div class="mb-4 col">
                    <h1 class="text-center">{{ __(is_company() ? 'cart.quote_items' : 'cart.cart') }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 cart-table">
                        <table class="table" cellspacing="0">
                            <thead>
                            <tr>
                                <th class="product-remove">&nbsp;</th>
                                <th class="product-thumbnail">&nbsp;</th>
                                <th class="product-name">{{ __('cart.product') }}</th>
                                <th class="product-quantity w-lg-15">{{ __('cart.quantity') }}</th>
                                @if(!is_company())
                                    <th class="product-price">{{__('cart.price')}}</th>
                                    <th class="product-subtotal">{{__('cart.total')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="text-center close_cart_new">
                                        <a style="cursor: pointer" data-url="{{ route('cart.remove', ['rowId' => $item->rowId]) }}" class="text-gray-32 font-size-26 deleteCart">×</a>
                                    </td>
                                    <td class="d-md-table-cell img_cart_view">
                                        @if($item->model)
                                            <a href="{{route('front.product', [generatedNestedSlug($item->model->category->ancestors()->pluck('slug')->toArray(), $item->model->category->slug), $item->model->slug])}}">
                                                <img loading="lazy" class="img-fluid max-width-100 p-1 border border-color-1"
                                                     src="{{ $item->model->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_width_100')['url'] }}"
                                                     alt="{{ $item->model->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt'] }}"
                                                     title="{{ $item->model->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title'] }}"
                                                >
                                            </a>
                                        @else
                                            <img loading="lazy" class="img-fluid max-width-100 p-1 border border-color-1" src="/assets/img/default_logo.jpg" />
                                        @endif
                                    </td>

                                    @if($item->model)
                                        <td data-title="{{ __('cart.product') }}">
                                            <a href="{{route('front.product', [generatedNestedSlug($item->model->category->ancestors()->pluck('slug')->toArray(), $item->model->category->slug), $item->model->slug])}}" class="text-gray-90">{{ $item->name }}</a>
                                        </td>
                                    @else
                                        <td data-title="{{ __('cart.product') }}">
                                            <a class="text-gray-90">{{ $item->name }}</a>
                                        </td>
                                    @endif

                                    @if($item->model)
                                        <td data-title="{{ __('cart.quantity') }}">
                                            <span class="sr-only">Quantity</span>
                                            <!-- Quantity -->
                                            <div class="border rounded-pill py-1 width-122 w-xl-80 px-3 border-color-1">
                                                <div class="js-quantity row align-items-center">
                                                    <div class="col">
                                                        <input class="js-result form-control h-auto border-0 rounded p-0 shadow-none" type="text" data-id="{{$item->id}}" data-row_id="{{ $item->rowId }}" value="{{ $item->qty }}">
                                                    </div>
                                                    <div class="col-auto pr-1">
                                                        <a class="js-minus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0">
                                                            <small class="fas fa-minus btn-icon__inner"></small>
                                                        </a>
                                                        <a class="js-plus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0">
                                                            <small class="fas fa-plus btn-icon__inner"></small>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Quantity -->
                                        </td>
                                    @else
                                        <td>-</td>
                                    @endif

                                    @if(!is_company())
                                        <td data-title="{{__('cart.price')}}">
                                            <span class="">{{__('general.sar') . ' ' . number_format($item->price, 2) }}</span>
                                        </td>
                                        <td data-title="{{__('cart.total')}}">
                                            <span class="">{{__('general.sar') . ' ' . number_format($item->subtotal, 2) }}</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="border-top space-top-2 justify-content-center">
                                    <div class="pt-md-3">
                                        <div class="d-block d-md-flex flex-center-between">
                                            @if(!is_company())
                                                <div class="mb-3 mb-md-0 w-xl-40">
                                                    <!-- Apply coupon Form -->
                                                    <form class="js-focus-state" action="{{ route('apply.coupon') }}" method="POST">
                                                        @csrf
                                                        <label class="sr-only">{{ __('cart.coupon_code') }}</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="code" placeholder="{{ __('cart.coupon_code') }}" id="couponCode" aria-label="Coupon code" aria-describedby="subscribeButtonExample2" required>
                                                            <div class="input-group-append">
                                                                <input type="submit" class="btn btn-block btn-dark px-4" value="{{ __('cart.apply_coupon') }}" />
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <!-- End Apply coupon Form -->
                                                </div>
                                            @endif
                                            <div class="d-md-flex">
                                                <button type="button" id="updateCart" class="btn btn-soft-secondary mb-3 mb-md-0 font-weight-normal px-5 px-md-4 px-lg-5 w-100 w-md-auto">{{ __(is_company() ? 'cart.update_quote' : 'cart.update_cart') }}</button>
                                                <a href="{{ route('checkout.index') }}" class="d-none d-lg-block btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-inline-block">{{ __(is_company() ? 'cart.get_prices' : 'cart.proceed_to_checkout') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                </div>
            </div>

            @if(!is_company())
                <div class="row rtl">
                    <div class="col-xl-5 col-lg-6  col-md-8 mb-5">
                        <div class="border-bottom border-color-1 mb-3">
                            <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">{{ __('cart.cart_totals') }}</h3>
                        </div>
                        <table class="table mb-3 mb-md-0">
                            <tbody>
                            <tr class="cart-subtotal">
                                <th>{{ __('cart.subtotal') }}</th>
                                <td data-title="Subtotal"><span class="amount" id="subtotal">{{ __('general.sar') . ' ' . Cart::subtotal() }}</span></td>
                            </tr>
                            <tr class="shipping">
                                <th>{{ __('cart.shipping') }}</th>
                                <td data-title="tax"><span class="amount" id="tax">{{ __('general.sar') . ' ' . cart_delivery() }}</span></td>
                            </tr>
                            <tr class="order-total">
                                <th>{{ __('cart.total') }}</th>
                                <td data-title="Total"><strong><span class="amount" id="total">{{ __('general.sar') . ' ' . cart_total() }}</span></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="border-top space-top-2 justify-content-center p_bn_cart">
                    <div class="d-md-flex">
                        <a href="{{ route('checkout.index') }}" class="d-block d-lg-none btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-inline-block">{{ __(is_company() ? 'cart.get_prices' : 'cart.proceed_to_checkout') }}</a>
                    </div>
                </div>


            @endif
        @else
            <h4 class="tit_cart_min">{{ __('cart.no_items_in_cart') }}</h4>
    </div>
    @endif
@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.deleteCart').on('click', function() {
                let url = $(this).data('url'),
                    button = $(this);

                $.ajax({
                    type: 'DELETE',
                    url: url,
                    data: { _token: '{{ csrf_token() }}' }
                })
                .done(function(response) {
                    if(response.status) {
                        if(response.count === 0)
                            window.location.reload();

                        $('.cartCount').html(response.count);
                        $('.cartTotal').html(response.total);
                        $('#total').html(response.total);
                        $('#tax').html(response.delivery);
                        $('#subtotal').html(response.subtotal);
                        button.closest('tr').remove();
                    }
                })
                .fail(function(response) {
                    console.log(response);
                })
            });

            $('#updateCart').on('click', function() {
                let url = '{{ route('cart.update') }}';

                $(".js-result").each(function(i, el) {
                    let qty = $(this).val(),
                        id = $(this).data('id'),
                        rowId = $(this).data('row_id');

                    $.ajax({
                        type: 'PATCH',
                        url: url,
                        data: {_token: "{{ csrf_token() }}", qty: qty, id: id, rowId: rowId}
                    })
                    .done(function(response) {
                        console.log(response);
                    })
                });

                //Reload the page. instead of updating data of the whole page!
                window.location.reload();
            });
        });
    </script>
@endsection
