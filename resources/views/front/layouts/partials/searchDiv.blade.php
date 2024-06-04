<div class="container rtl" id="SearchInnerDiv" style="position: relative">
    <div class="h_scroll" style="padding: 0;">
        <div class="container p-0">
            <div class="row">
                <div class="col-xl-12 col-wd-12gdot5" style="width:100%;">
                    <div class="block_search_check">
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade pt-1 show active" id="pills-one-example1" role="tabpanel" aria-labelledby="pills-one-example1-tab" data-target-group="groups">
                                <ul class="row list-unstyled products-group no-gutters" id="searchRegionGrid">
                                    @if(isset($suggested_products))
                                        @forelse($suggested_products as $search_product)
                                            <div class="col-12 col-md-12 col-wd-12gdot4 product-item">
                                                <li class="product-item remove-divider" style="width: 100%;">
                                                    <div class="product-item__outer w-100">
                                                        <a href="{{ route('front.product', [generatedNestedSlug($search_product->category->ancestors()->pluck('slug')->toArray(), $search_product->category->slug), $search_product->slug]) }}">
                                                            <div class="product-item__inner remove-prodcut-hover p-2 row">
                                                                <div class="product-item__body col-9 col-md-9">
                                                                    <div class="pr-lg-3">
                                                                        <h5 class="product-item__title">
                                                                            <div class="text-blue font-weight-bold" style="min-height: 0;">
                                                                                {{ $search_product->name }}
                                                                            </div>
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                                <div class="product-item__footer col-md-3 d-md-block">
                                                                    <div class="flex-center-between">
                                                                        <div class="font-size-12 text-gray-5">
                                                                            {{ $search_product->category->name }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </li>
                                            </div>
                                        @empty
                                            <div class="alert alert-warning col-12 pl-2 text-left">{{ __('search.no_records') }}</div>
                                        @endforelse
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
