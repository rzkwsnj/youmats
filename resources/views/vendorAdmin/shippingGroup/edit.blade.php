@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_shipping_group')}}</title>
@endsection
@section('content')
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.edit_shipping_group')}} ({{$shipping->name}})</h4>
                    <form action="{{route('vendor.shipping-group.update', [$shipping->id])}}" method="post">
                    <div class="card">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">{{__('vendorAdmin.name')}}</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{$shipping->name}}">
                            </div>
                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.specific_shipping_terms')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" id="clone-container">
                                    @foreach($shipping->prices as $key => $price)
                                        <div class="clone-element" data-iteration="{{$key}}">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label for="car_type">{{__('vendorAdmin.car_type')}}</label>
                                                        <input type="text" class="form-control" name="cars[{{$key}}][car_type]" id="car_type" value="{{$price['attributes']['car_type']}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>{{__('vendorAdmin.remove')}}</label>
                                                        <button class="form-control btn btn-danger btn-xs clone-remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clone-container-cities">
                                            @foreach($price['attributes']['cities'] as $innerKey => $row)
                                                <div class="row clone-element-cities">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="city">{{__('vendorAdmin.city')}}</label>
                                                            <select class="form-control" id="city" name="cars[{{$key}}][{{$innerKey}}][city]">
                                                                <option value="" disabled selected>{{__('vendorAdmin.cities_placeholder')}}</option>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}" @if($row['attributes']['city'] == $city->id) selected @endif>{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="quantity">{{__('vendorAdmin.quantity')}}</label>
                                                            <input type="number" class="form-control" id="quantity" name="cars[{{$key}}][{{$innerKey}}][quantity]" min="1" step="1" value="{{$row['attributes']['quantity']}}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="price">{{__('vendorAdmin.price')}}</label>
                                                            <input type="number" class="form-control" id="price" name="cars[{{$key}}][{{$innerKey}}][price]" min="0" step="0.05" value="{{$row['attributes']['price']}}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="time">{{__('vendorAdmin.time')}}</label>
                                                            <input type="number" class="form-control" id="time" name="cars[{{$key}}][{{$innerKey}}][time]" min="1" step="1" value="{{$row['attributes']['time']}}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label for="format">{{__('vendorAdmin.format')}}</label>
                                                            <select class="form-control" id="format" name="cars[{{$key}}][{{$innerKey}}][format]">
                                                                <option value="" disabled selected>{{__('vendorAdmin.format_placeholder')}}</option>
                                                                <option value="hour" @if($row['attributes']['format'] == 'hour') selected @endif>{{__('vendorAdmin.hour')}}</option>
                                                                <option value="day" @if($row['attributes']['format'] == 'day') selected @endif>{{__('vendorAdmin.day')}}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>{{__('vendorAdmin.remove_group')}}</label>
                                                            <button class="form-control btn btn-danger btn-xs clone-remove-cities">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-3">
                                                    <button type="button" class="btn btn-youmats btn-block clone-add-cities">{{__('vendorAdmin.add_city')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-12">
                                    <button type="button" id="clone-add" class="btn btn-youmats btn-block">{{__('vendorAdmin.add')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-youmats">{{__('vendorAdmin.submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js_additional')
    <script>
        $(document).ready(function () {
            let main_iteration = {{$key+1}},
                inner_iteration = {{$innerKey+1}};
            $('#clone-add').on('click', function () {
                let clone_element = `<div class="clone-element" data-iteration="`+main_iteration+`">
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="car_type">{{__('vendorAdmin.car_type')}}</label>
                            <input type="text" class="form-control" name="cars[`+main_iteration+`][car_type]" id="car_type">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{__('vendorAdmin.remove')}}</label>
                            <button class="form-control btn btn-danger btn-xs clone-remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="clone-container-cities"></div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <button type="button" class="btn btn-youmats btn-block clone-add-cities">{{__('vendorAdmin.add_city')}}</button>
                    </div>
                </div>
            </div>`;
                main_iteration++;
                $('#clone-container').append(clone_element);
            });
            $(document).on('click', '.clone-remove', function () {
                $(this).closest('.clone-element').remove();
            });

            $(document).on('click', '.clone-add-cities', function () {
                let iteration = $(this).closest('.clone-element').data('iteration');
                let clone_element_cities = `
                <div class="row clone-element-cities">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="city">{{__('vendorAdmin.city')}}</label>
                            <select class="form-control" id="city" name="cars[`+iteration+`][`+inner_iteration+`][city]">
                                <option value="" disabled selected>{{__('vendorAdmin.cities_placeholder')}}</option>
                                @foreach($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quantity">{{__('vendorAdmin.quantity')}}</label>
                                                        <input type="number" class="form-control" id="quantity" name="cars[`+iteration+`][`+inner_iteration+`][quantity]" min="1" step="1" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="price">{{__('vendorAdmin.price')}}</label>
                                                        <input type="number" class="form-control" id="price" name="cars[`+iteration+`][`+inner_iteration+`][price]" min="0" step="0.05" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="time">{{__('vendorAdmin.time')}}</label>
                                                        <input type="number" class="form-control" id="time" name="cars[`+iteration+`][`+inner_iteration+`][time]" min="1" step="1" />
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="format">{{__('vendorAdmin.format')}}</label>
                                                        <select class="form-control" id="format" name="cars[`+iteration+`][`+inner_iteration+`][format]">
                                                            <option value="" disabled selected>{{__('vendorAdmin.format_placeholder')}}</option>
                                                            <option value="hour">{{__('vendorAdmin.hour')}}</option>
                                                            <option value="day">{{__('vendorAdmin.day')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>{{__('vendorAdmin.remove')}}</label>
                                                        <button class="form-control btn btn-danger btn-xs clone-remove-cities">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                inner_iteration++;
                $(this).parent().parent().siblings('.clone-container-cities').append(clone_element_cities);
            });
            $(document).on('click', '.clone-remove-cities', function () {
                $(this).closest('.clone-element-cities').remove();
            });
        });
    </script>
@endsection
