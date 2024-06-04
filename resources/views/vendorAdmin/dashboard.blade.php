@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.dashboard')}}</title>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{__('vendorAdmin.dashboard')}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('vendorAdmin.home')}}</a></li>
                        <li class="breadcrumb-item active">{{__('vendorAdmin.dashboard')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.email')}}</span>
                            <span class="info-box-number" style="font-size: 14px">{{$vendor->email}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-industry"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.type')}}</span>
                            <span class="info-box-number">{{__('vendorAdmin.' . strtolower($vendor->type))}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.licenses')}}</span>
                            <span class="info-box-number">{{__('vendorAdmin.verify')}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-users"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.contacts')}}</span>
                            <span class="info-box-number">{{$contacts}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.products')}}</span>
                            <span class="info-box-number">{{$products}}</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{rand(5,10)}}0%"></div>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-code-branch"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.branches')}}</span>
                            <span class="info-box-number">{{$branches}}</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: {{rand(5,10)}}0%"></div>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-receipt"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.orders')}}</span>
                            <span class="info-box-number">{{$orders}}</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: {{rand(5,10)}}0%"></div>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.quotes')}}</span>
                            <span class="info-box-number">{{$quotes}}</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: {{rand(5,10)}}0%"></div>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-truck"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.shipping_groups')}}</span>
                            <span class="info-box-number">{{$shippingGroups}}</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{rand(5,10)}}0%"></div>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-gray"><i class="far fa-flag"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{__('vendorAdmin.address')}}</span>
                            <span class="info-box-number">{{$vendor->address}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
