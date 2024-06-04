@extends('front.layouts.master')
@section('metaTags')
    <title>YouMats | Login</title>
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
<div id="main">
    <section class="section-login">
        <div class="section-main">
            <div class="section-login-1 login-sides">
                <div class="section-login-1-main">

                    <h1 class="section-login-1-title">{{ __('general.youmats') }}</h1>
                    <p class="section-login-1-text">{{ __('general.slogan') }}</p>
                    <div class="section-login-1-img">
                        <img src="https://rvs-gradie-signup-page.vercel.app/Assets/iPhone-Mockup.png" alt="">
                    </div>

                </div>
            </div>
            <div class="section-login-2 login-sides">
                <div class="section-login-2-main">

                    <h1 class="section-login-2-title">{{ __('auth.login') }}</h1>
                    <form class="section-login-2-form" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="login-form-1">
                            <label for="input-email" class="align_text">{{ __('auth.email') }}</label>
                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                        </div>
                        <div class="login-form-2">
                            <label for="input-name" class="align_text">{{ __('auth.password_input') }}</label>
                            <div class="main-password">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                <a class="icon-view"><i class="fa fa-eye"></i></a>
                            </div>

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="login-form-4">
                            <input type="checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <p class="m-0">{{ __('auth.remember_me') }} </p>
                        </div>
                        <div class="login-form-submit-btn">
                            <button type="submit">{{ __('auth.login') }}</button>
                        </div>

                        @if (Route::has('password.request'))
                        <div class="login-form-5">
                             <p><a href="{{ route('password.request') }}">{{ __('auth.forget_password') }}</a></p>
                        </div>
                        @endif
                        <div class="login-form-5">
                            <p>{{ __('auth.not_member') }} <a href="{{route('register')}}">{{ __('auth.register') }}</a></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('extraScripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $("ul.nav-tab > li > a").on('shown.bs.tab', function(e) {
                    window.location.hash = $(e.target).attr('id');
                })

                let hash = window.location.hash;
                $('ul.nav-tab a[id="'+ hash + '"]').tab('show');

                $('.main-password').find('#password').each(function(index, input) {
                    var $input = $(input);
                    $input.parent().find('.icon-view').click(function() {
                        var change = "";
                        if ($(this).find('i').hasClass('fa-eye')) {
                            $(this).find('i').removeClass('fa-eye')
                            $(this).find('i').addClass('fa-eye-slash')
                            change = "text";
                        } else {
                            $(this).find('i').removeClass('fa-eye-slash')
                            $(this).find('i').addClass('fa-eye')
                            change = "password";
                        }
                        var rep = $("<input type='" + change + "' />")
                            .attr('id', $input.attr('id'))
                            .attr('name', $input.attr('name'))
                            .attr('class', $input.attr('class'))
                            .val($input.val())
                            .insertBefore($input);
                        $input.remove();
                        $input = rep;
                    }).insertAfter($input);
                });
            });
        });

        var nav = $('.nav_fixed');
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                nav.css({"position":"static"});
            }
        });

    </script>
@endsection
