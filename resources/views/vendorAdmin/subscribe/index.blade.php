@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.subscribe_title')}}</title>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{__('vendorAdmin.subscribe_title')}}</h1>
                    <p>{{__('vendorAdmin.membership_price')}}</p>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('vendorAdmin.home')}}</a></li>
                        <li class="breadcrumb-item active">{{__('vendorAdmin.subscribe_title')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content content-vendor-edit pt-2" id="membershipsCategories">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p>
                        @foreach($categories as $category)
                        <button class="btn btn-primary" type="button" data-toggle="collapse"
                                data-target="#cat{{$category->id}}">
                            {{$category->name}}
                        </button>
                        @endforeach
                    </p>
                </div>
                <div class="col-md-12">
                    @foreach($categories as $category)
                        <div class="collapse" id="cat{{$category->id}}" data-parent="#membershipsCategories">
                            <div class="card card-body pt-5">
                                <div class="container-fluid">
                                <div class="row">
                                    @foreach($category->memberships->where('status', true) as $membership)
                                        <div class="col-md-4">
                                            <form action="{{route('vendor.subscribe.upgrade')}}" method="get" enctype="multipart/form-data">
                                                <input type="hidden" name="membership_id" value="{{$membership->id}}">
                                                <input type="hidden" name="category_id" value="{{$category->id}}">
                                                <div class="card card_pay_m">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="style_box_pay">
                                                                <div class="col-md-12">
                                                                    <p><strong>{{$membership->name}}</strong></p>
                                                                    <p>{{$membership->price . ' ' . getCurrency('symbol')}}</p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <span>{!! $membership->desc !!}</span>
                                                                </div>
                                                                @php
                                                                    $isSubscribe = isSubscribe($current_subscribes, $category->id, $membership->id);
                                                                    $isSubscribeWithModel = isSubscribe($current_subscribes, $category->id, $membership->id, true);
                                                                @endphp
                                                                @if($isSubscribe)
                                                                <div class="col-md-12">
                                                                    <label class="label label-warning">{{\Carbon\Carbon::now()->diffInDays($isSubscribeWithModel->expiry_date, false) + 1 . ' ' . __('vendorAdmin.days_remaining')}}</label>
                                                                    <label class="label label-success">{{__('vendorAdmin.already_subscribed')}}</label>
                                                                </div>
                                                                @endif
                                                                <div class="col-md-12 mt-2">
                                                                    @if($isSubscribe)
                                                                        <input type="hidden" form="cancel_subscribe" name="membership_id" value="{{$membership->id}}" />
                                                                        <input type="hidden" form="cancel_subscribe" name="category_id" value="{{$category->id}}" />
                                                                        <button type="submit" class="btn btn-warning" form="cancel_subscribe">{{__('vendorAdmin.cancel_subscribe')}}</button>
                                                                        @if(\Carbon\Carbon::now()->diffInDays($isSubscribeWithModel->expiry_date, false) < 5)
                                                                            <button type="submit" class="btn btn-youmats">{{__('vendorAdmin.subscribe_renew')}}</button>
                                                                        @endif
                                                                    @else
                                                                        <button type="submit" class="btn btn-youmats">{{__('vendorAdmin.subscribe_now')}}</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <form action="{{route('vendor.subscribe.cancel')}}" method="post" onsubmit="return confirm('{{__('vendorAdmin.cancel_notice')}}');" id="cancel_subscribe">
        {{csrf_field()}}
    </form>
@endsection
