<div id="Phone" style="position: fixed;z-index: 101;width: 250px;direction: ltr;bottom: 125px;right: 0;"></div>
<div id="DrawingDiv" style="position: fixed;z-index: 101;width: 250px;direction: ltr;bottom: 125px;right: 0;"></div>
<footer>
    <div class="bg-primary py-3 text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-md-3 mb-lg-0">
                    <div class="row align-items-center">
                        <div class="col-auto flex-horizontal-center">
                            <i class="ec ec-newsletter font-size-40"></i>
                            <h2 class="font-size-20 mb-0 ml-3">{{__('general.subscribe_title')}}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <!-- Subscribe Form -->
                    <form class="js-form-message" id="subscribeForm">
                        @csrf
                        <label class="sr-only">{{__('general.subscribe_input')}}</label>
                        <div class="input-group input-group-pill">
                            <input type="email" class="form-control border-0 height-40" name="email" placeholder="{{__('general.subscribe_input')}}" aria-label="Email address" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-dark btn-sm-wide height-40 py-2">{{__('general.subscribe_button')}}</button>
                            </div>
                        </div>
                    </form>
                    <!-- End Subscribe Form -->
                </div>
            </div>
        </div>
    </div>

    <div class="pt-4 bg-gray-13">
        <div class="container mt-1">
            <div class="row">
                <div class="col-lg-5">
                    <div class="mb-6">
                        <a href="{{route('home')}}" class="d-inline-block">
                            <img loading="lazy" src="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}" height="45">
                        </a>
                    </div>
                    @if(is_company())
                    <div class="mb-4">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <i class="ec ec-support text-primary font-size-56"></i>
                            </div>
                            <div class="col pl-3">
                                <div class="font-size-13 font-weight-light rtl">{{__('general.footer_gotquestions')}}</div>
                                    <p style="direction: ltr;">
                                        <a href="tel:{{__('info.phone')}}" class="font-size-20 text-gray-90">{{__('info.phone')}}</a><br>
                                        <a href="tel:{{__('info.phone2')}}" class="font-size-20 text-gray-90">{{__('info.phone2')}}</a>
                                    </p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-1 font-weight-bold">{{__('general.footer_contact_info')}}</h6>
                        <address class="">
                            {{__('info.address')}}
                        </address>
                    </div>
                    <div class="my-4 my-md-4">
                        <ul class="list-inline mb-0 opacity-7">
                            <li class="list-inline-item mr-0">
                                <a class="btn font-size-20 btn-icon btn-soft-dark btn-bg-transparent rounded-circle" href="{{__('info.facebook')}}">
                                    <span class="fab fa-facebook-f btn-icon__inner"></span>
                                </a>
                            </li>
                            <li class="list-inline-item mr-0">
                                <a class="btn font-size-20 btn-icon btn-soft-dark btn-bg-transparent rounded-circle" href="{{__('info.googleplus')}}">
                                    <span class="fab fa-google btn-icon__inner"></span>
                                </a>
                            </li>
                            <li class="list-inline-item mr-0">
                                <a class="btn font-size-20 btn-icon btn-soft-dark btn-bg-transparent rounded-circle" href="{{__('info.twitter')}}">
                                    <span class="fab fa-twitter btn-icon__inner"></span>
                                </a>
                            </li>
                            <li class="list-inline-item mr-0">
                                <a class="btn font-size-20 btn-icon btn-soft-dark btn-bg-transparent rounded-circle" href="{{__('info.instagram')}}">
                                    <span class="fab fa-github btn-icon__inner"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-12 col-md mb-4 mb-md-0">
                            <h6 class="mb-3 font-weight-bold">{{__('general.footer_find_it_fast')}}</h6>
                            <!-- List Group -->
                            <ul class="list-group list-group-flush list-group-borderless mb-0 list-group-transparent">
                                @foreach($footer_categories->take(7) as $category)
                                    <li>
                                        <a class="list-group-item list-group-item-action" href="{{ route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)]) }}">{{ $category->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <!-- End List Group -->
                        </div>

                        <div class="col-12 col-md mb-4 mb-md-0">
                            <!-- List Group -->
                            <ul class="list-group list-group-flush list-group-borderless mb-0 list-group-transparent">
                                @foreach($footer_categories->skip(7)->take(7) as $category)
                                    <li>
                                        <a class="list-group-item list-group-item-action" href="{{ route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)]) }}">{{ $category->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <!-- End List Group -->
                        </div>

                        <div class="col-12 col-md mb-4 mb-md-0">
                            <h6 class="mb-3 font-weight-bold">{{__('general.footer_customer_care')}}</h6>
                            <!-- List Group -->
                            <ul class="list-group list-group-flush list-group-borderless mb-0 list-group-transparent">
                                <li><a class="list-group-item list-group-item-action" href="#">{{ __('footer.account') }}</a></li>
{{--                                <li><a class="list-group-item list-group-item-action" href="#">{{ __('footer.wishlist') }}</a></li>--}}
                                <li><a class="list-group-item list-group-item-action" href="{{route('front.team.index')}}">{{ __('footer.team') }}</a></li>
                                @foreach($pages as $page)
                                <li><a class="list-group-item list-group-item-action" href="{{route('front.page.index', [$page->slug])}}">{{$page->title}}</a></li>
                                @endforeach
                                <li><a class="list-group-item list-group-item-action" href="{{route('front.faqs.page')}}">{{ __('footer.faq') }}</a></li>
                            </ul>
                            <!-- End List Group -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-14 py-2">
        <div class="container">
            <div class="flex-center-between d-block d-md-flex">
                <div class="mb-3 mb-md-0">&copy; <a href="#" class="font-weight-bold text-gray-90">{{env('APP_NAME')}}</a> - {{__('general.footer_all_rights_reserved')}}</div>
                <div class="text-md-right">
                    <span class="d-inline-block bg-white border rounded p-1">
                        <img loading="lazy" alt="Mada card logo icon" class="max-width-6" src="{{asset('assets/img')}}/mada.png">
                    </span>
                    <span class="d-inline-block bg-white border rounded p-1">
                        <img loading="lazy" alt="Master card logo icon" class="max-width-5" src="{{asset('assets/img')}}/patment-icon_1.png">
                    </span>
                    <span class="d-inline-block bg-white border rounded p-1">
                        <img loading="lazy" alt="Visa card logo icon" class="max-width-5" src="{{asset('assets/img')}}/patment-icon_2.png">
                    </span>
                </div>
            </div>
        </div>
    </div>

</footer>
<aside id="sidebarContent" class="u-sidebar u-sidebar__lg" aria-labelledby="sidebarNavToggler">
    <div class="u-sidebar__scroller">
        <div class="u-sidebar__container">
            <div class="js-scrollbar u-header-sidebar__footer-offset pb-3">
                <!-- Toggle Button -->
                <div class="d-flex align-items-center pt-4 px-7">
                    <button type="button" class="close ml-auto"
                            aria-controls="sidebarContent"
                            aria-haspopup="true"
                            aria-expanded="false"
                            data-unfold-event="click"
                            data-unfold-hide-on-scroll="false"
                            data-unfold-target="#sidebarContent"
                            data-unfold-type="css-animation"
                            data-unfold-animation-in="fadeInRight"
                            data-unfold-animation-out="fadeOutRight"
                            data-unfold-duration="500">
                        <i class="ec ec-close-remove"></i>
                    </button>
                </div>
                <!-- End Toggle Button -->

                <!-- Content -->
                <div class="js-scrollbar u-sidebar__body">
                    <div class="u-sidebar__content u-header-sidebar__content">
                        <form id="inquireForm" enctype="multipart/form-data">
                            @csrf
                            <div id="login" data-target-group="idForm">
                                <header class="text-center mb-7">
                                    <h2 class="h4 mb-0">{{__('general.quotation_title')}}</h2>
                                    <p>{{__('general.quotation_subtitle')}}</p>
                                </header>

                                <div class="form-group">
                                    <div class="js-form-message js-focus-state">
                                        <label class="sr-only">{{__('general.quotation_company_name')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <span class="fas fa-building"></span>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" @if(auth()->check()) value="{{ auth()->user()->name }}" @endif name="company_name" placeholder="{{__('general.quotation_company_name')}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="js-form-message js-focus-state">
                                        <label class="sr-only">{{__('general.quotation_contact_person')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" name="name" placeholder="{{__('general.quotation_contact_person')}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="js-form-message js-focus-state">
                                        <label class="sr-only">{{__('general.quotation_email')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" >
                                                    <span class="fas fa-envelope"></span>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" @if(auth()->check()) value="{{ auth()->user()->email }}" @endif name="email" placeholder="{{__('general.quotation_email')}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="js-form-message js-focus-state">
                                        <label class="sr-only">{{__('general.quotation_phone')}}</label>
                                        <div class="input-group">
                                            <input type="tel" class="form-control phoneNumber"
                                                   @if(auth()->check()) value="{{ auth()->user()->phone }}" @endif>
                                            <input type="hidden" class="fullPhoneNumber" name="quotation_phone">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="js-form-message js-focus-state">
                                        <label class="sr-only">{{__('general.quotation_message')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <span class="fas fa-sticky-note"></span>
                                                </span>
                                            </div>
                                            <textarea class="form-control" name="message" placeholder="{{__('general.quotation_message')}}"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <!-- actual upload which is hidden -->
                                    <input type="file" name="file" id="actual-btn" hidden/>

                                    <!-- our custom upload button -->
                                    <label class="ub_file" for="actual-btn">{{ __('general.choose_file') }}</label>

                                    <!-- name of file chosen -->
                                    <span id="file-chosen">{{ __('general.no_file_chosen') }}</span>
                                </div>

                                <div class="mb-2">
                                    <button type="submit" class="btn btn-block btn-sm btn-primary transition-3d-hover">{{__('general.quotation_button')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>


@if(!Route::is('front.category'))
<a class="js-go-to u-go-to" href="#" data-position='{"bottom": 125, "right": 15}' data-type="fixed" data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp" data-hide-effect="slideOutDown">
    <span class="fas fa-arrow-up u-go-to__inner"></span>
</a>


    @if(isset($widget_phone))

        @if(Route::is('front.product') AND isset($product->vendor->contacts))
            <a target="_blank" onclick="SetUpCall({{$widget_phone}})" type="button" class="widget log" style="border: none;" data-log="call" data-id="{{ $product->id }}" data-type="product" data-url="{{ url()->current() }}">
                <i class="fas fa-phone"></i>
            </a>
        @else
            <a target="_blank" onclick="SetUpCall({{$widget_phone}})" type="button" class="widget" style="border: none;" >
                <i class="fas fa-phone"></i>
            </a>
        @endif

    @else

        @if(Route::is('front.product') AND isset($product->vendor->contacts))
            <a target="_blank" target="_blank" href="tel:{{ nova_get_setting('widget_phone')}}" type="button" class="widget log" style="border: none;" data-log="call" data-id="{{ $product->id }}" data-type="product" data-url="{{ url()->current() }}">
                <i class="fas fa-phone"></i>
            </a>
        @else
            <a target="_blank" target="_blank" href="tel:{{ nova_get_setting('widget_phone')}}" type="button" class="widget" style="border: none;">
                <i class="fas fa-phone"></i>
            </a>
        @endif

    @endif

    @if(Route::is('front.product'))
        <a target="_blank" href="{{$widget_whatsapp ?? 'https://wa.me/' . nova_get_setting('widget_whatsapp')}}"class="widget whatsapp log" data-log="chat" data-id="{{ $product->id }}" data-type="product" data-url="{{ url()->current() }}">
            <i class="fab fa-whatsapp"></i>
        </a>
    @else
        <a target="_blank" href="{{$widget_whatsapp ?? 'https://wa.me/' . nova_get_setting('widget_whatsapp')}}"class="widget whatsapp">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

@endif
