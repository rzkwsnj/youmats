@if ($view == 'grid')
    <div class="product-item__outer h-100">
        <div class="product-item__inner">
            <div class="product-item__body">
                <div class="mb-2 px-2"><a
                        href="{{ route('front.category', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug)]) }}"
                        class="font-size-12 text-gray-5">{{ $product->category->name }}</a></div>
                <h5 class="mb-1 product-item__title px-2">
                    <a href="{{ route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug]) }}"
                        class="text-blue font-weight-bold">
                        {{ Str::limit($product->name, 72) }}
                    </a>
                </h5>
                <div class="mb-2 px-2">
                    <div style="height: 32px">
                        @if (isset($product->vendor) && $product->subscribe && !$product->vendor->sold_by_youmats)
                            <a href="{{ route('vendor.show', [$product->vendor->slug]) }}"
                                class="badge badge-primary text-white"
                                style="
                                border-radius: 1px;
                                font-weight: bold;
                                padding: 0 0.4em;
                                background-color: #333;
                                font-size: 75%;
                                line-height: 32px;
                            ">{{ \Str::limit($product->vendor->name, 20) }}</a>
                        @endif
                    </div>
                    <a href="{{ route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug]) }}"
                        class="d-block text-center">
                        <img loading="lazy" class="img-fluid"
                            src="{{ $product->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url'] }}"
                            alt="{{ $product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt'] }}"
                            title="{{ $product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title'] }}">
                    </a>
                </div>
                <div class="font-size-12 productDesc px-2 pb-2 mb-2">
                    {{ Str::replace(['&nbsp;', '&times;'], [' ', '×'], Str::limit(strip_tags($product->short_desc), 107)) }}
                </div>
                <div class="custom-price-border px-2 pb-2 mb-2">
                    @if (!$product->category->hide_availability)
                        <div class="font-size-14">
                            @if (is_company())
                                <span class="text-green font-weight-bold">{{ __('product.in_stock') }}</span>
                            @else
                                @if ($product->stock && $product->stock >= $product->min_quantity)
                                    <span class="text-green font-weight-bold">{{ __('product.in_stock') }}</span>
                                @else
                                    <span class="text-red font-weight-bold">{{ __('product.out_of_stock') }}</span>
                                @endif
                            @endif
                        </div>
                    @endif
                    @if (!$product->category->hide_delivery_status)
                        @if (!is_company())
                            @if (isset($product->delivery))
                                <div>{{ __('product.delivery_to_your_city_in_category') }}:
                                    <b>{{ getCurrentCityName() }}</b>
                                </div>
                            @else
                                <div style="color:#ff0000;">{{ __('product.no_delivery_in_category') }}:
                                    {{ getCurrentCityName() }}</div>
                            @endif
                        @endif
                    @endif
                    @if ($product->type == 'product' && !is_company() && $product->price)
                        <div class="product-price">{{ getCurrency('symbol') }} {{ $product->formatted_price }}</div>
                    @endif
                </div>
                @if (is_company())
                    <div class="px-2">
                        {!! cartOrChat($product, false) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@elseif($view == 'list')
    {{-- <li class="product-item remove-divider"> --}}
    {{--    <div class="product-item__outer w-100"> --}}
    {{--        <div class="product-item__inner remove-prodcut-hover py-4 row"> --}}
    {{--            <div class="product-item__header col-6 col-md-2"> --}}
    {{--                <div class="mb-2"> --}}
    {{--                    <a href="{{route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug])}}" class="d-block text-center"> --}}
    {{--                        <img loading="lazy" class="img-fluid" src="{{$product->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url']}}" alt="{{$product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['alt']}}" title="{{ $product->getFirstMediaUrlOrDefault(PRODUCT_PATH)['title'] }}"> --}}
    {{--                    </a> --}}
    {{--                </div> --}}
    {{--            </div> --}}
    {{--            <div class="product-item__body col-6 col-md-7"> --}}
    {{--                <div class="pr-lg-10"> --}}
    {{--                    <div class="mb-2"><a href="{{route('front.category', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug)])}}" class="font-size-12 text-gray-5">{{$product->category->name}}</a></div> --}}
    {{--                    <h5 class="mb-2 product-item__title"><a href="{{route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug])}}" class="text-blue font-weight-bold">{{$product->name}}</a></h5> --}}
    {{--                    @if ($product->type == 'product' && !is_company() && $product->price) --}}
    {{--                        <div class="prodcut-price d-md-none"> --}}
    {{--                            <div class="text-gray-100">{{getCurrency('symbol')}} {{$product->formatted_price}}</div> --}}
    {{--                        </div> --}}
    {{--                    @endif --}}
    {{--                    <div class="font-size-12 p-0 mb-4 d-none d-md-block"> --}}
    {{--                        {!! $product->short_desc !!} --}}
    {{--                    </div> --}}
    {{--                    <div class="text-gray-20 mb-2 font-size-12">{{__('general.sku')}}: {{$product->SKU}}</div> --}}
    {{--                    @if (auth()->guard('admin')->check() && isset($product->vendor)) --}}
    {{--                        <div class="text-gray-20 mb-2 font-size-12">{{__('general.vendor')}}: {{$product->vendor->name}}</div> --}}
    {{--                    @endif --}}
    {{--                    <div class="mb-3 d-none d-md-block"> --}}
    {{--                        <a class="d-inline-flex align-items-center small font-size-14" href="#"> --}}
    {{--                            <div class="text-warning mr-2"> --}}
    {{--                                @for ($i = 1; $i <= $product->rate; $i++) --}}
    {{--                                    <small class="fas fa-star"></small> --}}
    {{--                                @endfor --}}
    {{--                                @for ($i = 5; $i > $product->rate; $i--) --}}
    {{--                                    <small class="far fa-star text-muted"></small> --}}
    {{--                                @endfor --}}
    {{--                            </div> --}}
    {{--                            <span class="text-secondary">(40)</span> --}}
    {{--                        </a> --}}
    {{--                    </div> --}}
    {{--                </div> --}}
    {{--            </div> --}}
    {{--            <div class="product-item__footer col-md-3 d-md-block"> --}}
    {{--                <div class="mb-2 flex-center-between"> --}}
    {{--                    <div class="prodcut-price"> --}}
    {{--                        @if ($product->type == 'product' && !is_company() && $product->price) --}}
    {{--                        <div class="text-gray-100">{{getCurrency('symbol')}} {{$product->formatted_price}}</div> --}}
    {{--                        @endif --}}
    {{--                    </div> --}}
    {{--                    @if (!Auth::guard('vendor')->check()) --}}
    {{--                        <div class="flex-horizontal-center justify-content-between justify-content-wd-center flex-wrap border-top pt-3"> --}}
    {{--                            {!! cartOrChat($product, false) !!} --}}
    {{--                            @if (Request::route()->getName() != 'wishlist.index') --}}
    {{--                                <a data-url="{{ route('wishlist.add', ['product' => $product]) }}" class="text-gray-6 font-size-13 btn-add-wishlist pointer"><i class="ec ec-favorites mr-1 font-size-15"></i> {{__('product.wishlist')}}</a> --}}
    {{--                            @else --}}
    {{--                                <div class="prodcut-add-cart"> --}}
    {{--                                    <button data-url="{{ route('wishlist.remove', ['rowId' => $rowId]) }}" class="btn-remove-wishlist btn-danger transition-3d-hover"><i class="ec ec-close-remove"></i></button> --}}
    {{--                                </div> --}}
    {{--                            @endif --}}
    {{--                        </div> --}}
    {{--                    @endif --}}
    {{--                </div> --}}
    {{--            </div> --}}
    {{--        </div> --}}
    {{--    </div> --}}
    {{-- </li> --}}
@endif
