@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_order')}}</title>
@endsection
@section('content')
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.edit_order')}} ({{$item->order->order_id}})</h4>
                    <form method="post" action="{{route('vendor.order.update')}}">
                        <div class="card">
                            {{csrf_field()}}
                            <input type="hidden" name="item_id" value="{{$item->id}}" />
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('vendorAdmin.payment_status')}}</label><br/>
                                            <label class="badge
                                            @if($item->payment_status == 'pending')
                                                badge-warning
                                            @elseif($item->payment_status == 'refunded')
                                                badge-danger
                                            @elseif($item->payment_status == 'completed')
                                                badge-success
                                            @endif
                                            ">
                                                {{$item->payment_status}}
                                            </label>
                                        </div>
                                    </div>
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="refused_note">{{__('vendorAdmin.refused_note')}}</label>
                                            <textarea id="refused_note" name="refused_note" class="form-control">{{$item->refused_note}}</textarea>
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
                                        <label>{{__('vendorAdmin.order_id')}}:</label> {{$item->order->order_id}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_name')}}:</label> {{$item->order->name}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_email')}}:</label> {{$item->order->email}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_phone')}}:</label> {{$item->order->phone}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_phone2')}}:</label> {{$item->order->phone2}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_address')}}:</label> {{$item->order->address}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_building_number')}}:</label> {{$item->order->building_number}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_street')}}:</label> {{$item->order->street}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_district')}}:</label> {{$item->order->district}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_city')}}:</label> {{$item->order->city}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.user_notes')}}:</label> {{$item->order->notes}}
                                    </div>
                                </div>
                                @if($item->order->coupon_code)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.coupon_code')}}:</label> {{$item->order->coupon_code}}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.product_details')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_name')}}:</label> {{$item->product->name}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_quantity')}}:</label> {{$item->quantity}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_price')}}:</label> {{__('general.sar') . ' ' . $item->price}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_delivery')}}:</label> {{__('general.sar') . ' ' . $item->delivery}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.product_total_price')}}:</label> {{__('general.sar') . ' ' . (($item->quantity * $item->price) + $item->delivery)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.shipping_details')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>{{__('vendorAdmin.shipping_car')}}</th>
                                        <th>{{__('vendorAdmin.shipping_quantity')}}</th>
                                        <th>{{__('vendorAdmin.shipping_price')}}</th>
                                        <th>{{__('vendorAdmin.shipping_time')}}</th>
                                        <th>{{__('vendorAdmin.shipping_count')}}</th>
                                        <th>{{__('vendorAdmin.shipping_payload')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->delivery_cars as $car)
                                        <tr>
                                            <td>{{$car['car']}}</td>
                                            <td>{{$car['quantity']}}</td>
                                            <td>{{$car['price']}}</td>
                                            <td>{{$car['time'] . ' ' . $car['format']}}</td>
                                            <td>{{$car['count']}}</td>
                                            <td>{{$car['payload']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4>{{__('vendorAdmin.payment_info')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.payment_method')}}:</label> {{$item->order->payment_method}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.reference_number')}}:</label> {{$item->order->reference_number}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.card_number')}}:</label> {{$item->order->card_number}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.card_type')}}:</label> {{$item->order->card_type}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.card_name')}}:</label> {{$item->order->card_name}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.card_exp_date')}}:</label> {{$item->order->card_exp_date}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('vendorAdmin.transaction_date')}}:</label> {{$item->order->transaction_date}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
