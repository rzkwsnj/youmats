<link rel="preload" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"></noscript>

<link rel="preload" href="{{front_url()}}/assets/vendor/font-awesome/css/fontawesome-all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{front_url()}}/assets/vendor/font-awesome/css/fontawesome-all.min.css"></noscript>

<link rel="stylesheet" href="{{mix('/assets/css/app.min.css')}}">

@if(\LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
    <link rel="stylesheet" href="{{mix('/assets/css/rtl.min.css')}}">
@endif

<link rel="preload" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css"></noscript>

@yield('extraStyles')
