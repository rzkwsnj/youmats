<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/fontawesome-free/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- JQVMap -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/jqvmap/jqvmap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/adminlte.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/daterangepicker/daterangepicker.css">
<!-- summernote -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/summernote/summernote-bs4.min.css">
<!-- Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- Toastr -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/toastr/toastr.css">
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<!-- Custom Style -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/custom-vendor-before.css">
<!-- RTL Style -->
@if(\LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
    <!-- Bootstrap 4 RTL -->
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
    <!-- Custom style for RTL -->
    <link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/rtl-vendor.css">
    <link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/custom-style-rtl.css">

@endif
<!-- Custom Style -->
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/custom-vendor-after.css">
<link rel="stylesheet" href="{{asset('vendorAdminAssets')}}/dist/css/custom-style.css">
