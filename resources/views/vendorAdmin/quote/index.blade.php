@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.quotes')}}</title>
@endsection
@section('content')
    <div class="pt-2">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="tit_main_v">{{__('vendorAdmin.quotes')}}</h4>
                </div>
            </div>

            <div class="card card--vendor edit-head-table-vendor">
                <div class="card-body">
                    <table id="example1" class="table" style="width: 100%">
                        <thead>
                        <tr class="head--table--vendor">
                            <th class="text-center">{{__('vendorAdmin.quote_no')}}</th>
                            <th class="text-center">{{__('vendorAdmin.name')}}</th>
                            <th class="text-center">{{__('vendorAdmin.status')}}</th>
                            <th class="text-center">{{__('vendorAdmin.date')}}</th>
                            <th class="text-center">{{__('vendorAdmin.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{$item->quote->quote_no}}</td>
                                <td>{{$item->quote->name}}</td>
                                <td class="btn-ship">
                                    <label class="badge
                                    @if($item->quote->status == 'pending')
                                        badge-warning
                                    @elseif($item->quote->status == 'shipping')
                                        badge-primary
                                    @elseif($item->quote->status == 'completed')
                                        badge-success
                                    @elseif($item->quote->status == 'refused')
                                        badge-danger
                                    @endif
                                    ">
                                        {{$item->quote->status}}
                                    </label>
                                </td>
                                <td>{{date('d M Y H:i A', strtotime($item->quote->created_at))}}</td>
                                <td>
                                    <a href="{{route('vendor.quote.view', [$item->quote->id])}}" class="btn btn-youmats btn-xs">{{__('vendorAdmin.view_button')}}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
