@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.view_quote')}}</title>
@endsection
@section('content')
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.user_info')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.quote_no')}}:</label> {{$item->quote->quote_no}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_name')}}:</label> {{$item->quote->name}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_email')}}:</label> {{$item->quote->email}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_phone')}}:</label> {{$item->quote->phone}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_address')}}:</label> {{$item->quote->address}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_notes')}}:</label> {{$item->quote->notes}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.products_details')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            @foreach($item->quote->items as $loop_item)
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_name')}}:</label> {{$loop_item->product->name}}
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_quantity')}}:</label> {{$loop_item->quantity}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
