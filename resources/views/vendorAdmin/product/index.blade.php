@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.products')}}</title>
@endsection
@section('content')
    <div class="pt-2">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6">
                    <h4 class="tit_main_v">{{__('vendorAdmin.products')}}</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{route('vendor.product.create')}}" class="btn btn-sm mb-3 btn-youmats tit-head-branch btn--vend"> <i class="fa fa-plus"></i> {{__('vendorAdmin.add_button')}}</a>
                </div>
            </div>



            <div class="card card--vendor edit-head-table-vendor">
                <div class="card-body">
                    <table id="example1" class="table" style="width: 100%">
                        <thead>
                            <tr class="head--table--vendor">
                                <th class="text-center">#</th>
                                <th class="text-center">{{__('vendorAdmin.name')}}</th>
                                <th class="text-center">{{__('vendorAdmin.category')}}</th>
                                <th class="text-center">{{__('vendorAdmin.price')}}</th>
                                <th class="text-center">{{__('vendorAdmin.views')}}</th>
                                <th class="text-center">{{__('vendorAdmin.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$product->name}}</td>
                                    <td>{{$product->category->name}}</td>
                                    <td>{{$product->price}} {{__('vendorAdmin.sar')}}</td>
                                    <td>{{$product->views}}</td>
                                    <td>
                                        <a href="{{route('vendor.product.edit', [$product->id])}}" class="btn btn-youmats btn-xs">{{__('vendorAdmin.edit_button')}}</a>
                                        @if($product->active)
                                        <a target="_blank" href="{{route('front.product', [generatedNestedSlug($product->category->getRelation('ancestors')->pluck('slug')->toArray(), $product->category->slug), $product->slug])}}" class="btn btn-success btn-xs">{{__('vendorAdmin.view_front')}}</a>
                                        @endif
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
