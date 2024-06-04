<!-- Featured Vendors -->
@if(count($featuredVendors) > 0)
    <div class="container mb-8">
        <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
            <h2 class="section-title section-title__full mb-0 pb-2 font-size-22">{{__('homee.vendors_title')}}</h2>
        </div>

        <div class="js-slick-carousel u-slick my-1"
             data-arrows-classes="d-none d-lg-inline-block u-slick__arrow-normal u-slick__arrow-centered--y"
             data-arrow-left-classes="fa fa-angle-left u-slick__arrow-classic-inner--left z-index-9"
             data-arrow-right-classes="fa fa-angle-right u-slick__arrow-classic-inner--right"
             data-slick='{
                 "autoplay": true,
                 "infinite": true,
                 "slidesToShow": 5
                 @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                 ,"rtl": true
                 @endif
             }'
             data-responsive='[{
                "breakpoint": 992,
                "settings": {
                    "slidesToShow": 2
                }
             }, {
                "breakpoint": 768,
                "settings": {
                    "slidesToShow": 1
                }
             }]'
        >
            @foreach($featuredVendors as $vendor)
                <div class="js-slide img_vend">
                    <a href="{{ route('vendor.show', [$vendor->slug]) }}" class="link-hover__brand">
                        <img loading="lazy" class="img-fluid m-auto max-height-50" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_height_50')['url'] }}" alt="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['alt']}}" title="{{$vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO)['title']}}">
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
