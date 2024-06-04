@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.branches')}}</title>
@endsection
@section('content')
    <div class="pt-2">

        <div class="col-md-12">


            <div class="row">
                <div class="col-md-6">
                    <h4 class="tit_main_v">{{__('vendorAdmin.branches')}}</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{route('vendor.branch.create')}}" class="btn btn-sm mb-3 btn-youmats tit-head-branch btn--vend">{{__('vendorAdmin.add_button_branch')}}</a>
                </div>
            </div>

            <div class="card card--vendor edit-head-table-vendor">
                <div class="card-body">
                    <table id="example1" class="table responsive" style="width: 100%">
                        <thead>
                            <tr class="head--table--vendor">
                                <th class="text-center">#</th>
                                <th class="text-center">{{__('vendorAdmin.name')}}</th>
                                <th class="text-center">{{__('vendorAdmin.city')}}</th>
                                <th class="text-center">{{__('vendorAdmin.phone')}}</th>
                                <th class="text-center">{{__('vendorAdmin.fax')}}</th>
                                <th class="text-center">{{__('vendorAdmin.address')}}</th>
                                <th class="text-center">{{__('vendorAdmin.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $branch)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$branch->name}}</td>
                                    <td>{{$branch->city->name}}</td>
                                    <td>{{$branch->phone_number}}</td>
                                    <td>{{$branch->fax}}</td>
                                    <td>{{$branch->address}}</td>
                                    <td>
                                        <a href="{{route('vendor.branch.edit', [$branch->id])}}" class="btn btn-youmats btn-xs">{{__('vendorAdmin.edit_button')}}</a>
                                        <form style="display: inline-block" method="post" action="{{route('vendor.branch.delete', [$branch->id])}}">
                                            {{csrf_field()}}
                                            {{method_field('DELETE')}}
                                            <button class="btn btn-danger btn-xs" type="submit">{{__('vendorAdmin.delete_button')}}</button>
                                        </form>
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
