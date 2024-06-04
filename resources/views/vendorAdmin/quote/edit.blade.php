@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_quote')}}</title>
@endsection
@section('content')
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.edit_quote')}} ({{$item->quote->quote_no}})</h4>
                    <form method="post" action="{{route('vendor.quote.update')}}">
                        <div class="card">
                            {{csrf_field()}}
                            <input type="hidden" name="item_id" value="{{$item->id}}" />
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">{{__('vendorAdmin.status')}}</label>
                                            <select id="status" class="form-control" name="status">
                                                <option value="pending" @if($item->status == 'pending') selected @endif>{{__('vendorAdmin.pending')}}</option>
                                                <option value="shipping" @if($item->status == 'shipping') selected @endif>{{__('vendorAdmin.shipping_option')}}</option>
                                                <option value="completed" @if($item->status == 'completed') selected @endif>{{__('vendorAdmin.completed')}}</option>
                                                <option value="refused" @if($item->status == 'refused') selected @endif>{{__('vendorAdmin.refused')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-youmats">{{__('vendorAdmin.submit')}}</button>
                    </form>
                </div>
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.user_info')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.quote_no')}}:</label> {{$item->quote->quote_no}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_name')}}:</label> {{$item->quote->name}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_email')}}:</label> {{$item->quote->email}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_phone')}}:</label> {{$item->quote->phone}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_address')}}:</label> {{$item->quote->address}}
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_name')}}:</label> {{$loop_item->product->name}}
                                    </div>
                                </div>
                                <div class="col-md-6">
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
