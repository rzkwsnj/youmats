@extends('front.layouts.master')
@section('metaTags')
    <link rel="canonical" href="{{url()->current()}}" />
@endsection
@section('content')
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"  itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="{{route('home')}}"><span itemprop="name">{{__('general.home')}}</span></a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page"><span itemprop="name">{{ __('vendor.vendors') }}</span>
                            <meta itemprop="position" content="2" />
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>

    <div class="mb-6 bg-md-transparent py-6">
        <div class="container">
            <div class="mb-6">
                <div class="d-flex justify-content-between align-items-center border-bottom border-color-1 flex-lg-nowrap flex-wrap mb-4 rtl">
                    <h3 class="section-title section-title__full mb-0 pb-2 font-size-22">{{ __('vendor.logos') }}</h3>
                </div>
                <ul class="row list-unstyled products-group no-gutters mb-6 rtl">
                    @foreach($vendors as $vendor)
                        <li class="col-6 col-md-2 col-xl-1gdot7 product-item">
                            <div class="product-item__outer h-100 w-100">
                                <div class="product-item__inner px-xl-4 p-3">
                                    <div class="product-item__body pb-xl-2">
                                        <div class="mb-2">
                                                <a href="{{ route('vendor.show', [$vendor->slug]) }}" class="d-block text-center"><img loading="lazy" class="img-fluid" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_150_150')['url'] }}" alt="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['alt'] }}"></a>
                                        </div>
                                        <h5 class="text-center mb-1 product-item__title"><a href="{{ route('vendor.show', [$vendor->slug]) }}" class="font-size-15 text-gray-90">{{ $vendor->name }}</a></h5>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                {{ $vendors->onEachSide(0)->links() }}
            </div>
        </div>
    </div>
@endsection
