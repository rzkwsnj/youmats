<div id="SearchFace" ></div>
<header id="header" class="u-header u-header-left-aligned-nav">
    <div class="u-header__section shadow-none" id="u-header__section">

        <!-------- Top header -------->
        <div class="u-header-topbar d-none d-lg-block bg-gray-2 border-0 py-2 d-xl-block">
            <div class="container">
                <div class="d-flex align-items-center">
                    <div class="topbar-right ml-auto st_nav_mob">
                        <ul class="list-inline mb-0">
{{--                            @if(!Auth::guard('vendor')->check())--}}
{{--                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">--}}
{{--                                    <a href="@if(Cart::instance('wishlist')->count() > 0) {{ route('wishlist.index') }} @else # @endif" class="u-header-topbar__nav-link"><i class="ec ec-favorites mr-1"></i> {{__('general.wishlist')}} </a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
                            @if($currencies)
                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                    <div class="d-flex align-items-center">
                                        <!-- Language -->
                                        <div class="position-relative">
                                            <a id="languageDropdownInvoker" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#languageDropdown" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                                <span class="d-none d-sm-none">{{getCurrency('symbol')}}</span>
                                                <span class="d-sm-inline-flex align-items-center">
                                                    <i class="far fa-money-bill-alt mr-1"></i>
                                                    {{getCurrency('code')}} ({{getCurrency('symbol')}})
                                                </span>
                                            </a>
                                            <div id="languageDropdown" class="dropdown-menu dropdown-unfold" aria-labelledby="languageDropdownInvoker">
                                                @foreach($currencies as $currency)
                                                <a class="dropdown-item active currency_button" data-code="{{$currency->code}}" href="#">
                                                    <img loading="lazy" width="20px" alt="{{$currency->getFirstMediaUrlOrDefault(CURRENCY_PATH)['alt']}}" src="{{$currency->getFirstMediaUrlOrDefault(CURRENCY_PATH, 'size_20_20')['url']}}" />&nbsp;
                                                    {{$currency->code}} @if($currency->symbol) ({{$currency->symbol}}) @endif
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        <!-- End Language -->
                                    </div>
                                </li>
                            @endif

                            <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                <div class="d-flex align-items-center">
                                    <!-- Language -->
                                    <div class="position-relative">
                                        <a id="languageDropdownInvoker2" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#languageDropdown1" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                            <span class="d-sm-inline-flex align-items-center">
                                                <i class="fas fa-globe-americas mr-1 font-size-14"></i>
                                                {{ LaravelLocalization::getCurrentLocaleNative() }}
                                            </span>
                                        </a>
                                        <div id="languageDropdown1" class="dropdown-menu dropdown-unfold" aria-labelledby="languageDropdownInvoker2">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                            <a class="dropdown-item {{$localeCode}}" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">{{ $properties['native'] }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- End Language -->
                                </div>
                            </li>

                            <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border
                                        u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single userType-switch"
                                data-toggle="tooltip" data-placement="bottom" title="{{__('general.welcome_message')}}">
                                <div class="d-flex align-items-center">
                                    <!-- Language -->
                                    <div class="position-relative">
                                        <a id="userTypeDropdownInvoker2" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#userTypeDropdown1" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                        <span class="d-sm-inline-flex align-items-center">
                                            <i class="ec ec-user mr-1"></i>
                                            {{__('general.'.\Illuminate\Support\Facades\Session::get('userType'))}}
                                        </span>
                                        </a>
                                        <div id="userTypeDropdown1" class="dropdown-menu dropdown-unfold" aria-labelledby="userTypeDropdownInvoker2">
                                            <a class="dropdown-item typeIntroduceButton" data-url="{{route('front.introduce', ['individual'])}}">{{__('general.continue_as_individual')}}</a>
                                            <a class="dropdown-item typeIntroduceButton" data-url="{{route('front.introduce', ['company'])}}">{{__('general.continue_as_company')}}</a>
                                        </div>
                                    </div>
                                    <!-- End Language -->
                                </div>
                            </li>

                            @if(Auth::guard('web')->check() && !Auth::guard('vendor')->check())
                            <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                <div class="d-flex align-items-center">
                                    <!-- Language -->
                                    <div class="position-relative">
                                        <a id="profileDropdownInvoker2" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#profileDropdown1" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                            <span class="d-sm-inline-flex align-items-center">
                                                <i class="ec ec-user mr-1"></i> {{auth('web')->user()->name}}
                                            </span>
                                        </a>
                                        <div id="profileDropdown1" class="dropdown-menu dropdown-unfold" aria-labelledby="profileDropdownInvoker2">
                                            <a class="dropdown-item" href="{{route('front.user.profile')}}">{{__('general.profile')}}</a>
                                            <form class="dropdown-item inline-block p-0" style="cursor: pointer" action="{{route('logout')}}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">{{__('general.logout')}}</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- End Language -->
                                </div>
                            </li>
                            @endif

                            @if(Auth::guard('vendor')->check() && !Auth::guard('web')->check())
                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                    <div class="d-flex align-items-center">
                                        <!-- Language -->
                                        <div class="position-relative">
                                            <a id="profileDropdownInvoker2" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#profileDropdown1" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                            <span class="d-sm-inline-flex align-items-center">
                                                <i class="ec ec-user mr-1"></i> {{auth('vendor')->user()->name}}
                                            </span>
                                            </a>
                                            <div id="profileDropdown1" class="dropdown-menu dropdown-unfold" aria-labelledby="profileDropdownInvoker2">
                                                <a class="dropdown-item" href="{{ route('vendor.product.index') }}">{{__('general.profile')}}</a>
                                                <form class="dropdown-item inline-block p-0" style="cursor: pointer" action="{{route('vendor.logout')}}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">{{__('general.logout')}}</button>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Language -->
                                    </div>
                                </li>
                            @endif

                            @if(!auth()->guard('vendor')->check() && !auth()->guard('web')->check())
                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                    <div class="d-flex align-items-center">
                                        <!-- Register -->
                                        <div class="position-relative">
                                            <a id="registerDropDownInvoker" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#registerDropDown" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                                <span class="d-sm-inline-flex align-items-center"> <i class="ec ec-user mr-1"></i>{{__('general.user_register')}}</span>
                                            </a>
                                            <div id="registerDropDown" class="dropdown-menu dropdown-unfold" aria-labelledby="registerDropDownInvoker">
                                                <a href="{{route('register')}}" class="dropdown-item">{{__('general.user_register')}}</a>
                                                <a href="{{route('vendor.register')}}" class="dropdown-item">{{__('general.vendor_register')}}</a>
                                            </div>
                                        </div>
                                        <!-- End Register -->
                                    </div>
                                </li>
                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border u-header-topbar__nav-item-no-border u-header-topbar__nav-item-border-single">
                                    <div class="d-flex align-items-center">
                                        <!-- Register -->
                                        <div class="position-relative">
                                            <a id="loginDropDownInvoker" data-toggle="dropdown" class="dropdown-nav-link dropdown-toggle d-flex align-items-center u-header-topbar__nav-link font-weight-normal" href="javascript:;" aria-haspopup="true" aria-expanded="false" data-unfold-event="hover" data-unfold-target="#loginDropDown" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                                <span class="d-sm-inline-flex align-items-center"> <i class="ec ec-user mr-1"></i>{{__('general.user_login')}}</span>
                                            </a>
                                            <div id="loginDropDown" class="dropdown-menu dropdown-unfold" aria-labelledby="loginDropDownInvoker">
                                                <a href="{{route('login')}}" class="dropdown-item">
                                                    {{__('general.user_login')}}
                                                </a>
                                                <a href="{{route('vendor.login')}}" class="dropdown-item">{{__('general.vendor_login')}}
                                                </a>
                                            </div>
                                        </div>
                                        <!-- End Register -->
                                    </div>
                                </li>
                            @endif
                            @if(auth()->guard('admin')->check())
                                <li class="list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">
                                    <a href="{{url('/management')}}" target="_blank" class="badge badge-danger">{{__('general.login_as_admin')}}</a>
                                </li>
                            @endif
                            @if(is_company())
                                <li class="btn_iffer_price list-inline-item mr-0 u-header-topbar__nav-item u-header-topbar__nav-item-border">
                                    <a id="sidebarNavToggler" href="javascript:;" role="button" class="u-header-topbar__nav-link" aria-controls="sidebarContent" aria-haspopup="true" aria-expanded="false" data-unfold-event="click" data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarContent" data-unfold-type="css-animation" data-unfold-animation-in="fadeInRight" data-unfold-animation-out="fadeOutRight" data-unfold-duration="500">
                                        {{__('general.get_quote')}}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-------- End Top header -------->

        <!-------- logo & menu -------->
        <div class="py-2 py-xl-4 bg-primary-down-lg nav_fixed">
            <div class="container my-0dot5 my-xl-0">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <nav class="navbar navbar-expand u-header__navbar py-0 justify-content-xl-between">
                            <a class="order-1 order-xl-0 navbar-brand u-header__navbar-brand u-header__navbar-brand-center" href="{{route('home')}}" aria-label="">
                                <img loading="lazy" src="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}"
                                     alt="{{ (json_decode(nova_get_setting('site_name'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}"
                                     title="{{ (json_decode(nova_get_setting('site_name'))->{LaravelLocalization::getCurrentLocale()}) ?? env('APP_NAME') }}"
                                >
                            </a>
                            <button id="sidebarHeaderInvokerMenu" type="button" class="d-block d-md-none d-lg-none navbar-toggler d-block btn u-hamburger mr-3 mr-xl-0"
                                    aria-controls="sidebarHeader" aria-haspopup="true" aria-expanded="false" data-unfold-event="click"
                                    data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarHeader1" data-unfold-type="css-animation"
                                    data-unfold-animation-in="fadeInLeft" data-unfold-animation-out="fadeOutLeft" data-unfold-duration="500">
                                <span id="hamburgerTriggerMenu" class="u-hamburger__box">
                                    <span class="u-hamburger__inner"></span>
                                </span>
                            </button>
                        </nav>
                        <aside id="sidebarHeader1" class="u-sidebar u-sidebar--left" aria-labelledby="sidebarHeaderInvokerMenu">
                            <div class="u-sidebar__scroller">
                                <div class="u-sidebar__container">
                                    <div class="u-header-sidebar__footer-offset pb-0">

                                        <div class="position-absolute top-0 right-0 z-index-2 pt-4 pr-7">
                                            <button type="button" class="close ml-auto"
                                                    aria-controls="sidebarHeader"
                                                    aria-haspopup="true"
                                                    aria-expanded="false"
                                                    data-unfold-event="click"
                                                    data-unfold-hide-on-scroll="false"
                                                    data-unfold-target="#sidebarHeader1"
                                                    data-unfold-type="css-animation"
                                                    data-unfold-animation-in="fadeInLeft"
                                                    data-unfold-animation-out="fadeOutLeft"
                                                    data-unfold-duration="500">
                                                <span aria-hidden="true"><i class="ec ec-close-remove text-gray-90 font-size-20"></i></span>
                                            </button>
                                        </div>

                                        <div class="js-scrollbar u-sidebar__body">
                                            <div id="headerSidebarContent" class="u-sidebar__content u-header-sidebar__content">

                                                <a class="d-flex ml-0 navbar-brand u-header__navbar-brand u-header__navbar-brand-vertical" href="#" aria-label="">
                                                    <img loading="lazy" src="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_45')['url'] }}">
                                                </a>


                                                <!-------- Top header -------->
                                                <ul class="u-header-collapse__nav nav_mob_new">
                                                        @if($currencies)
                                                        <li class="u-has-submenu u-header-collapse__submenu">
                                                            <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-1" data-target="#top-header-nav-1">
                                                                <span class="d-none d-sm-none">{{getCurrency('symbol')}}</span>
                                                                <span class="d-sm-inline-flex align-items-center">
                                                                    <i class="far fa-money-bill-alt mr-1"></i>
                                                                    {{getCurrency('code')}} ({{getCurrency('symbol')}})
                                                                </span>
                                                            </a>
                                                            <div id="top-header-nav-1" class="collapse" data-parent="#headerSidebarContent">
                                                                <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                    <li>
                                                                        @foreach($currencies as $currency)
                                                                            <a class="dropdown-item active currency_button" data-code="{{$currency->code}}" href="#">
                                                                                <img loading="lazy" width="20px" alt="{{$currency->getFirstMediaUrlOrDefault(CURRENCY_PATH)['alt']}}" src="{{$currency->getFirstMediaUrlOrDefault(CURRENCY_PATH, 'size_20_20')['url']}}" />&nbsp;
                                                                                {{$currency->code}} @if($currency->symbol) ({{$currency->symbol}}) @endif
                                                                            </a>
                                                                        @endforeach
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        @endif
                                                        <li class="u-has-submenu u-header-collapse__submenu">
                                                            <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-2" data-target="#top-header-nav-2">
                                                                <span class="d-sm-inline-flex align-items-center">
                                                                    <i class="fas fa-globe-americas mr-1 font-size-14"></i>
                                                                    {{ LaravelLocalization::getCurrentLocaleNative() }}
                                                                </span>
                                                            </a>
                                                            <div id="top-header-nav-2" class="collapse" data-parent="#headerSidebarContent">
                                                                <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                    <li>
                                                                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                                            <a class="dropdown-item {{$localeCode}}" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">{{ $properties['native'] }}</a>
                                                                        @endforeach
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>

                                                        <li class="u-has-submenu u-header-collapse__submenu">
                                                            <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-3" data-target="#top-header-nav-3">
                                                                <span class="d-sm-inline-flex align-items-center">
                                                                    <i class="ec ec-user mr-1"></i>
                                                                    {{__('general.'.\Illuminate\Support\Facades\Session::get('userType'))}}
                                                                </span>
                                                            </a>
                                                            <div id="top-header-nav-3" class="collapse" data-parent="#headerSidebarContent">
                                                                <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                    <li>
                                                                        <a class="dropdown-item typeIntroduceButton" data-url="{{route('front.introduce', ['individual'])}}">{{__('general.continue_as_individual')}}</a>
                                                                        <a class="dropdown-item typeIntroduceButton" data-url="{{route('front.introduce', ['company'])}}">{{__('general.continue_as_company')}}</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>

                                                        @if(Auth::guard('web')->check() && !Auth::guard('vendor')->check())
                                                            <li class="u-has-submenu u-header-collapse__submenu">
                                                                <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-user" data-target="#top-header-nav-user">
                                                                <span class="d-sm-inline-flex align-items-center">
                                                                    <i class="ec ec-user mr-1 font-size-14"></i>
                                                                    {{auth('web')->user()->name}}
                                                                </span>
                                                                </a>
                                                                <div id="top-header-nav-user" class="collapse" data-parent="#headerSidebarContent">
                                                                    <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                        <li><a class="dropdown-item" href="{{route('front.user.profile')}}">{{__('general.profile')}}</a></li>
                                                                        <li>
                                                                            <form class="dropdown-item" style="cursor: pointer" action="{{route('logout')}}" method="POST">
                                                                                @csrf
                                                                                <button type="submit" class="dropdown-item" style="padding: 0 !important;">{{__('general.logout')}}</button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>
                                                        @endif

                                                        @if(Auth::guard('vendor')->check() && !Auth::guard('web')->check())
                                                            <li class="u-has-submenu u-header-collapse__submenu">
                                                                <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-vendor" data-target="#top-header-nav-vendor">
                                                                    <span class="d-sm-inline-flex align-items-center">
                                                                        <i class="ec ec-user mr-1 font-size-14"></i>
                                                                        {{auth('vendor')->user()->name}}
                                                                    </span>
                                                                </a>
                                                                <div id="top-header-nav-vendor" class="collapse" data-parent="#headerSidebarContent">
                                                                    <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                        <li><a class="dropdown-item" href="{{ route('vendor.product.index') }}">{{__('general.profile')}}</a></li>
                                                                        <li>
                                                                            <form class="dropdown-item" style="cursor: pointer" action="{{route('vendor.logout')}}" method="POST">
                                                                                @csrf
                                                                                <button type="submit" class="dropdown-item" style="padding: 0 !important;">{{__('general.logout')}}</button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>
                                                        @endif

                                                        @if(!auth()->guard('vendor')->check() && !auth()->guard('web')->check())
                                                        <li class="u-has-submenu u-header-collapse__submenu">
                                                            <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-nav-7" data-target="#top-header-nav-7">
                                                                <span class="d-sm-inline-flex align-items-center"> <i class="ec ec-user mr-1"></i>{{__('general.user_register')}}</span>
                                                            </a>
                                                            <div id="top-header-nav-7" class="collapse" data-parent="#headerSidebarContent">
                                                                <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                    <li>
                                                                        <a href="{{route('register')}}" class="dropdown-item">{{__('general.user_register')}}</a>
                                                                        <a href="{{route('vendor.register')}}" class="dropdown-item">{{__('general.vendor_register')}}</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        <li class="u-has-submenu u-header-collapse__submenu">
                                                            <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer collapsed" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="top-header-login" data-target="#top-header-login">
                                                                <span class="d-sm-inline-flex align-items-center"> <i class="ec ec-user mr-1"></i>{{__('general.user_login')}}</span>
                                                            </a>
                                                            <div id="top-header-login" class="collapse" data-parent="#headerSidebarContent">
                                                                <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                    <li>
                                                                        <a href="{{route('login')}}" class="dropdown-item">{{__('general.user_login')}}</a>
                                                                        <a href="{{route('vendor.login')}}" class="dropdown-item">{{__('general.vendor_login')}}</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        @endif

                                                        @if(is_company() || auth()->guard('vendor')->check())
                                                            <li class="btn_offer_price_nav u-has-submenu u-header-collapse__submenu">
                                                                <a id="sidebarNavToggler" href="javascript:;" role="button" class="u-header-topbar__nav-link" aria-controls="sidebarContent" aria-haspopup="true" aria-expanded="false" data-unfold-event="click" data-unfold-hide-on-scroll="false" data-unfold-target="#sidebarContent" data-unfold-type="css-animation" data-unfold-animation-in="fadeInRight" data-unfold-animation-out="fadeOutRight" data-unfold-duration="500">
                                                                    {{__('general.get_quote')}}
                                                                </a>
                                                            </li>
                                                        @endif
                                                </ul>
                                                <!-------- End Top header -------->

                                                <ul id="headerSidebarList" class="u-header-collapse__nav">
                                                    @foreach($categories->take(20) as $category)
                                                    <li class="u-has-submenu u-header-collapse__submenu">
                                                        <a class="u-header-collapse__nav-link u-header-collapse__nav-pointer" href="javascript:;" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="headerSidebarHomeCollapse" data-target="#{{$category->slug}}">
                                                            {{$category->name}}
                                                        </a>

                                                        <div id="{{$category->slug}}" class="collapse" data-parent="#headerSidebarContent">
                                                            <ul id="headerSidebarHomeMenu" class="u-header-collapse__nav-list">
                                                                @foreach($category->children as $child)
                                                                <li><a class="u-header-collapse__submenu-nav-link" href="{{route('front.category', [generatedNestedSlug($child->getRelation('ancestors')->pluck('slug')->toArray(), $child->slug)])}}">{{$child->name}}</a></li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <!-- End List -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <div class="col d-none d-xl-block">
                        <!-- Nav -->
                        <nav class="js-mega-menu navbar navbar-expand-md u-header__navbar u-header__navbar--no-space">
                            <!-- Navigation -->
                            <div id="navBar" class="collapse navbar-collapse u-header__navbar-collapse">
                                <ul class="navbar-nav u-header__navbar-nav">

                                    <li class="nav-item u-header__nav-item">
                                        <a class="nav-link u-header__nav-link" href="{{route('home')}}">{{__('general.home')}}</a>
                                    </li>
                                    <li class="nav-item u-header__nav-item">
                                        <a class="nav-link u-header__nav-link" href="{{route('front.product.all')}}">{{__('general.all_products')}}</a>
                                    </li>
                                    <li class="nav-item u-header__nav-item">
                                        <a class="nav-link u-header__nav-link" href="{{ route('vendor.index') }}">{{__('general.our_partners')}}</a>
                                    </li>
                                    <li class="nav-item u-header__nav-item">
                                        <a class="nav-link u-header__nav-link" href="{{route('front.faqs.page')}}">{{__('general.faq')}}</a>
                                    </li>
                                    <li class="nav-item u-header__nav-item">
                                        <a class="nav-link u-header__nav-link" href="{{route('front.contact.page')}}">{{__('general.contact_us')}}</a>
                                    </li>

                                </ul>
                            </div>
                            <!-- End Navigation -->
                        </nav>
                        <!-- End Nav -->
                    </div>
                    @if(is_company())
                    <div class="d-none d-xl-block col-md-auto">
                        <div class="d-flex">
                            <i class="ec ec-support font-size-50 text-primary"></i>
                            <div class="ml-2">
                                <div class="phone">
                                    <strong>{{__('general.support')}}</strong>
                                    <a href="tel:{{__('info.phone')}}" class="text-gray-90" style="direction:ltr; display:inline-block">{{__('info.phone')}}</a>
                                </div>
                                <div class="email">
                                    {{__('general.quotation_email')}}: <a href="mailto:{{__('info.email')}}" class="text-gray-90">{{__('info.email')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="d-xl-none col col-xl-auto text-right text-xl-left pl-0 pl-xl-3 position-static rtl_menu_ar">
                        <div class="d-inline-flex">
                            <ul class="d-flex list-unstyled mb-0 align-items-center">
                                <!-- Search -->
                                <li class="col d-none px-2 px-sm-3 position-static">
                                    <a id="searchClassicInvoker" class="font-size-22 text-gray-90 text-lh-1 btn-text-secondary" href="javascript:;" role="button" aria-controls="searchClassic" aria-haspopup="true" aria-expanded="false" data-unfold-target="#searchClassic" data-unfold-type="css-animation" data-unfold-duration="300" data-unfold-delay="300" data-unfold-hide-on-scroll="true" data-unfold-animation-in="slideInUp" data-unfold-animation-out="fadeOut">
                                        <span class="ec ec-search"></span>
                                    </a>
                                </li>
                                <!-- End Search -->
                                <li class="col d-none d-xl-block">
                                    <a href="#" class="text-gray-90">
                                        <i class="font-size-22 ec ec-favorites"></i>
                                    </a>
                                </li>

                                <div class="d-block d-lg-none d-xl-none">
                                    <button type="button" class="choose_city btn btn-primary btn-xs"
                                            data-toggle="modal" data-target=".change_city_modal" style="margin: 5px 0 0 !important;"
                                    >{{getCurrentCityName()}}</button>
                                </div>

                                @if(is_individual())
                                <li class="col d-xl-none px-2 px-sm-3">
                                    <a class="userTypeSwitcher typeIntroduceButton"
                                       data-url="{{route('front.introduce', ['company'])}}"
                                       data-toggle="tooltip" data-placement="bottom" title="{{__('general.welcome_message_mobile_individual_mode')}}">
                                        <span class="d-sm-inline-flex align-items-center">
                                            <i class="font-size-22 fas fa-users"></i>
                                        </span>
                                    </a>
                                </li>
                                @elseif(is_company())
                                <li class="col d-xl-none px-2 px-sm-3">
                                    <a class="userTypeSwitcher typeIntroduceButton"
                                       data-url="{{route('front.introduce', ['individual'])}}"
                                       data-toggle="tooltip" data-placement="bottom" title="{{__('general.welcome_message_mobile_company_mode')}}">
                                        <span class="d-sm-inline-flex align-items-center">
                                            <i class="font-size-22 far fa-building"></i>
                                        </span>
                                    </a>
                                </li>
                                @endif

                                <li class="col d-xl-none px-2 px-sm-3">
                                    @if(Auth::guard('web')->check() && !Auth::guard('vendor')->check())
                                        <a href="{{route('front.user.profile')}}" class="text-gray-90">
                                            <i class="font-size-22 ec ec-user"></i>
                                        </a>
                                    @elseif(!Auth::guard('vendor')->check())
                                        <a href="{{route('login')}}" class="text-gray-90">
                                            <i class="font-size-22 ec ec-user"></i>
                                        </a>
                                    @endif
                                </li>
                                <li class="col pr-xl-0 px-2 px-sm-3">
                                    <a href="{{ route('cart.show') }}" class="text-gray-90 position-relative d-flex">
                                        <i class="font-size-22 ec ec-shopping-bag"></i>
                                        <span class="width-22 height-22 bg-dark position-absolute d-flex align-items-center justify-content-center rounded-circle left-12 top-8 font-weight-bold font-size-12 text-white cartCount">{{ Cart::instance('cart')->count() }}</span>
                                        @if(!is_company())
                                            <span class="d-none d-xl-block font-weight-bold font-size-16 text-gray-90 ml-3 cartTotal">{{ getCurrency('symbol'). ' ' . Cart::instance('cart')->total() }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search-Form -->

        <div class="box_search_nav d-block d-lg-none">
            <div class="input-group" id="SearchBarMoblie">
                <input type="search" autocomplete="off" class="fils_search_nav form-control py-2 pl-5 font-size-15 border-0 height-40 rounded-left-pill"
                       id="searchProductInputMobile" placeholder="{{ __('general.search_placeholder') }}"
                       aria-label="Search for Products" aria-describedby="searchProduct1" required>
                <div class="input-group-append">
                    <!-- End Select -->
                    <button class="btn btn-primary height-40 py-2 px-3 mt-0 rounded-right-pill" style="box-shadow: none;" type="button" id="searchProductBtnMobile">
                        <span class="ec ec-search font-size-24" id="searchButtonSpan"></span>
                    </button>
                </div>
            </div>
        </div>
        <!-- End Search-Form -->

        <!-------- search & cart -------->
        <div class="d-none d-xl-block bg-primary" id="searchAndCartDiv">
            <div class="container">
                <div class="row align-items-stretch min-height-50 rtl">
                    <div class="col-md-auto d-none d-xl-flex align-items-end">
                        <div class="max-width-270 min-width-270">
                            <div id="basicsAccordion">
                                <div class="card border-0 rounded-0">
                                    <div class="card-header bg-primary rounded-0 card-collapse border-0" id="basicsHeadingOne">
                                        <button type="button" class="btn-link btn-remove-focus btn-block d-flex card-btn py-3 text-lh-1 px-4 shadow-none btn-primary rounded-top-lg border-0 font-weight-bold text-gray-90 collapsed" data-toggle="collapse" data-target="#basicsCollapseOne" aria-expanded="false" aria-controls="basicsCollapseOne">
                                            <span class="pl-1 text-gray-110">{{__('general.all_categories')}}</span>
                                            <span class="text-gray-110 ml-3">
                                                <span class="ec ec-arrow-down-search"></span>
                                            </span>
                                        </button>
                                    </div>
                                    <div id="basicsCollapseOne" class="collapse vertical-menu v1" aria-labelledby="basicsHeadingOne" data-parent="#basicsAccordion">
                                        <div class="card-body p-0">
                                            <nav class="js-mega-menu navbar navbar-expand-xl u-header__navbar u-header__navbar--no-space hs-menu-initialized">
                                                <div id="navBar" class="collapse navbar-collapse u-header__navbar-collapse">
                                                    <ul class="navbar-nav u-header__navbar-nav border-primary border-top-0">
                                                        @foreach($categories->take(17) as $category)
                                                        <li class="nav-item hs-has-mega-menu u-header__nav-item" data-event="hover" data-position="left">
                                                            <a id="{{$category->slug}}" class="nav-link u-header__nav-link u-header__nav-link-toggle"
                                                               href="{{route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)])}}" aria-haspopup="true" aria-expanded="false">{{$category->name}}</a>

                                                            <div class="hs-mega-menu vmm-tfw u-header__sub-menu" aria-labelledby="{{$category->slug}}">
                                                                <div class="vmm-bg">
                                                                    <img loading="lazy" class="img-fluid" src="{{$category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_width_290')['url']}}" alt="{{$category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['alt'] }}" title="{{$category->getFirstMediaUrlOrDefault(CATEGORY_PATH)['title'] }}">
                                                                </div>
                                                                <div class="row u-header__mega-menu-wrapper">
                                                                    <div class="col mb-3 mb-sm-0">
                                                                        <span class="u-header__sub-menu-title">{{$category->name}}</span>
                                                                        <ul class="u-header__sub-menu-nav-group mb-3">
                                                                            @foreach($category->children->take(7) as $child)
                                                                            <li><a class="nav-link u-header__sub-menu-nav-link" href="{{route('front.category', [generatedNestedSlug($child->getRelation('ancestors')->pluck('slug')->toArray(), $child->slug)])}}">{{$child->name}}</a></li>
                                                                            @endforeach
                                                                            <li>
                                                                                <a class="nav-link u-header__sub-menu-nav-link u-nav-divider border-top pt-2 flex-column align-items-start"
                                                                                   href="{{route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)])}}">
                                                                                    <div class="">{{__('general.all_categories')}}</div>
                                                                                    <div class="u-nav-subtext font-size-11 text-gray-30">{{__('general.discover_more_products')}}</div>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    @if(count($category->children) > 7)
                                                                    <div class="col mb-3 mb-sm-0">
                                                                        <span class="u-header__sub-menu-title">{{$category->name}}</span>
                                                                        <ul class="u-header__sub-menu-nav-group mb-3">
                                                                            @foreach($category->children->skip(7)->take(7) as $child)
                                                                            <li><a class="nav-link u-header__sub-menu-nav-link" href="{{route('front.category', [generatedNestedSlug($child->getRelation('ancestors')->pluck('slug')->toArray(), $child->slug)])}}">{{$child->name}}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </li>
                                                        @endforeach
                                                        @if(count($categories) > 17)
                                                            <li class="nav-item hs-has-mega-menu u-header__nav-item" data-event="hover" data-position="left">
                                                                <a id="otherCategories" class="nav-link u-header__nav-link u-header__nav-link-toggle"
                                                                   href="#" aria-haspopup="true" aria-expanded="false">{{__('general.other_categories')}}</a>

                                                                <div class="hs-mega-menu vmm-tfw u-header__sub-menu" aria-labelledby="otherCategories">
                                                                    <div class="row u-header__mega-menu-wrapper">
                                                                        <div class="col mb-3 mb-sm-0">
                                                                            <span class="u-header__sub-menu-title">{{__('general.other_categories')}}</span>
                                                                            <ul class="u-header__sub-menu-nav-group mb-3">
                                                                                @foreach($categories->skip(17)->take(10) as $category)
                                                                                    <li><a class="nav-link u-header__sub-menu-nav-link" href="{{route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)])}}">{{$category->name}}</a></li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                        @if(count($categories) > 27)
                                                                        <div class="col mb-3 mb-sm-0">
                                                                            <span class="u-header__sub-menu-title">{{__('general.other_categories')}}</span>
                                                                            <ul class="u-header__sub-menu-nav-group mb-3">
                                                                                @foreach($categories->skip(27) as $category)
                                                                                    <li><a class="nav-link u-header__sub-menu-nav-link" href="{{route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)])}}">{{$category->name}}</a></li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Search Bar [START] -->
                    <div class="col d-none d-xl-block">
                        <label class="sr-only" for="searchProduct">{{__('general.search')}}</label>
                        <div class="input-group" id="SearchBar" style="margin-top: 4px;">
                            <input type="search" autocomplete="off" class="form-control py-2 pl-5 font-size-15 border-0 height-40" id="searchProductInput" placeholder="{{ __('general.search_placeholder') }}" aria-label="Search for Products" aria-describedby="searchProduct1" required>
                            <div class="input-group-append">
                                <button class="btn btn-dark height-40 py-2 px-2 rounded-right-pill" style="background-color: #febd69;border-color: #febd69;box-shadow: none;" type="button" id="searchProductBtn">
                                    <span class="ec ec-search font-size-24" id="searchButtonSpan" style="color: black;font-weight: bolder;"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Search Bar [END] -->
                    <div class="col-md-auto align-self-center">
                        <div class="d-flex">
                            <ul class="d-flex list-unstyled mb-0">
                                <li class="col pr-0">
                                    <a href="{{ route('cart.show') }}" class="text-gray-110 position-relative d-flex">
                                        <i class="font-size-22 ec ec-shopping-bag"></i>
                                        <span class="width-22 height-22 bg-dark position-absolute flex-content-center text-white rounded-circle left-12 top-8 font-weight-bold font-size-12 cartCount">{{ Cart::instance('cart')->count() }}</span>
                                        @if(!is_company())
                                            <span class="font-weight-bold font-size-16 text-gray-110 ml-3 cartTotal">{{ __('general.sar') . ' ' . cart_total() }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="searchDiv" class="d-none">
            @include('front.layouts.partials.searchDiv')
        </div>
    </div>
</header>
