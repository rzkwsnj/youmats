@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Wishlist</title>
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
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('home')}}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">{{ __('Wishlist') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="mb-6 bg-md-transparent py-6">
        <div class="container">
            @if(count($wishProducts) > 0)
                <div class="row mb-8">
                    <div class="col-xl-12">
                        <!-- Tab Content -->
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade pt-2 active show" id="list-view" role="tabpanel" aria-labelledby="list-view-tab" data-target-group="groups">
                                <ul class="d-block list-unstyled products-group prodcut-list-view-small">
                                    @foreach($wishProducts as $product)
                                        @include('front.layouts.partials.product_box', ['product' => $product->model, 'view' => 'list', 'rowId' => $product->rowId])
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <h4>{{ __('There are no item(s) added to your wishlist') }}</h4>
            @endif
        </div>
    </div>
@endsection
