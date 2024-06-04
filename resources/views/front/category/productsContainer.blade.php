<div class="d-block d-md-flex flex-center-between mb-3 rtl">
    <div class="text-left">
        <span class="font-size-25 mb-2 mb-md-0"></span>
        @if(isset($category))
        <input type="hidden" value="{{$category->id}}" id="categoryIdContainer">
        @endif
        <p class="font-size-14 text-gray-90 mb-0">{{__('general.showing')}} {{$products->firstItem()}}–{{$products->firstItem() + count($products->items()) -1}} {{__('general.of')}} {{$products->total()}} {{__('general.results')}}</p>
    </div>
    @if(is_individual())
    <div class="text-right">
        <select class="form-control form-control-sm" id="sort_select">
            <option selected value="">{{__('general.sort_placeholder')}}</option>
            <option value="-price" @if(request()->input('sort') == '-price') selected @endif>{{__('general.sort_price_high')}}</option>
            <option value="price" @if(request()->input('sort') == 'price') selected @endif>{{__('general.sort_price_low')}}</option>
            <option value="-delivery" @if(request()->input('sort') == '-delivery') selected @endif>{{__('general.sort_delivery_high')}}</option>
            <option value="delivery" @if(request()->input('sort') == 'delivery') selected @endif>{{__('general.sort_delivery_low')}}</option>
        </select>
    </div>
    @endif
</div>

<!-- Shop-control-bar -->
<div class="bg-gray-1 flex-center-between borders-radius-9 py-1">
    <div class="px-3 d-none">
        <ul class="nav nav-tab-shop" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="grid-view-tab" data-toggle="pill" href="#grid-view" role="tab" aria-controls="grid-view" aria-selected="false">
                    <div class="d-md-flex justify-content-md-center align-items-md-center">
                        <i class="fa fa-th"></i>
                    </div>
                </a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" id="list-view-tab" data-toggle="pill" href="#list-view" role="tab" aria-controls="list-view" aria-selected="true">--}}
{{--                    <div class="d-md-flex justify-content-md-center align-items-md-center">--}}
{{--                        <i class="fa fa-th-list"></i>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </li>--}}
        </ul>
    </div>
    <nav class="px-3 flex-horizontal-left text-gray-20 d-none">
        <a class="text-gray-30 font-size-20 mr-2" href="{{$products->previousPageUrl()}}">
            @if(app()->getLocale() == 'ar')
                &nbsp;→&nbsp;
            @else
                &nbsp;←&nbsp;
            @endif
        </a>
        <b>{{$products->currentPage()}} </b> &nbsp; {{__('general.of')}} {{$products->lastPage()}}
        <a class="text-gray-30 font-size-20 ml-2" href="{{$products->nextPageUrl()}}">
            @if(app()->getLocale() == 'ar')
                &nbsp;←&nbsp;
            @else
                &nbsp;→&nbsp;
            @endif
        </a>
    </nav>

        <div class="rtl ml-2 box--chan-c d-none d-lg-block d-xl-block">
            <button type="button" class="choose_city btn btn-primary btn-xs" data-toggle="modal" data-target=".change_city_modal">{{__('general.change_city_button')}}</button>
            <strong class="tit_check_in_city"> {{__('general.city_location_text')}}: {{getCurrentCityName()}}</strong>
            {{--        {{__('general.category_word_after_change_city_button')}}--}}
        </div>

    <div class="custom-control custom-switch">
        <input type="checkbox" name="is_price" class="custom-control-input" id="is_price" @if(request()->input('filter.is_price') == 'on') checked @endif>
        <label class="custom-control-label tit--custom--label" for="is_price"><strong class="tit_check_in">{{__('general.is_price')}}</strong></label>
    </div>

    <div class="custom-control custom-switch">
        <input type="checkbox" name="is_delivery" class="custom-control-input" id="is_delivery" @if(request()->input('filter.is_delivery') == 'on') checked @endif>
        <label class="custom-control-label tit--custom--label" for="is_delivery"> <strong class="tit_check_in">{{__('general.is_delivery')}}</strong></label>
    </div>
</div>
<!-- End Shop-control-bar -->

<!-- Tab Content -->
<div class="tab-content rtl" id="pills-tabContent">
    <div class="tab-pane fade pt-2 show active" id="grid-view" role="tabpanel" aria-labelledby="grid-view-tab" data-target-group="groups">
        <ul class="row list-unstyled products-group no-gutters" id="categoryProductGrid">
            @forelse($products as $product)
                <li class="col-6 col-md-3 col-wd-2gdot4 product-item">
                    @include('front.layouts.partials.product_box', ['product' => $product, 'view' => 'grid'])
                </li>
            @empty
                 <p class="alert alert-warning alert-block w-100">{{__('general.no_data')}}</p>
            @endforelse
        </ul>
    </div>
{{--    <div class="tab-pane fade pt-2" id="list-view" role="tabpanel" aria-labelledby="list-view-tab" data-target-group="groups">--}}
{{--        <ul class="d-block list-unstyled products-group prodcut-list-view-small" id="categoryProductList">--}}
{{--            @forelse($products as $product)--}}
{{--                @include('front.layouts.partials.product_box', ['product' => $product, 'view' => 'list'])--}}
{{--            @empty--}}
{{--                <p class="alert alert-warning alert-block w-100">{{__('general.no_data')}}</p>--}}
{{--            @endforelse--}}
{{--        </ul>--}}
{{--    </div>--}}
</div>
<!-- End Tab Content -->
<nav class="rtl d-md-flex justify-content-between align-items-center border-top pt-3" aria-label="Page navigation example">
    <ul class="pagination mb-0 pagination-shop justify-content-center justify-content-md-start">
        {{$products->onEachSide(0)->links()}}
    </ul>
</nav>
