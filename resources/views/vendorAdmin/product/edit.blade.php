@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_product')}}</title>
@endsection
@section('content')
    <section class="content content-vendor-edit pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.edit_product')}}</h4>
                    <form action="{{route('vendor.product.update', [$product->id])}}" method="post" enctype="multipart/form-data">
                        <div class="card">
                            {{csrf_field()}}
                            {{method_field('PUT')}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="category">{{__('vendorAdmin.category')}}</label>
                                            <select class="form-control" id="category">
                                                <option value="" selected disabled>{{__('vendorAdmin.category_placeholder')}}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}" @if($selected_category == $category->id) selected @endif>{{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="subCategory">{{__('vendorAdmin.subCategory')}}</label>
                                            <select class="form-control" name="category_id" id="subCategory">
                                                <option value="" selected disabled>{{__('vendorAdmin.subCategory_placeholder')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <nav>
                                            <div class="nav nav-languages" id="nav-tab" role="tablist">
                                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                    <a class="nav-link @if($loop->first) active @endif" id="nav-{{$localeCode}}-tab-name"
                                                       data-toggle="tab" href="#nav-{{$localeCode}}-name" role="tab" aria-controls="nav-{{$localeCode}}-name" aria-selected="false">{{ $properties['native'] }}</a>
                                                @endforeach
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                <div class="tab-pane fade @if($loop->first) show active @endif"
                                                     id="nav-{{$localeCode}}-name" role="tabpanel" aria-labelledby="nav-{{$localeCode}}-tab-name">
                                                    <div class="form-group">
                                                        <label for="name-{{$localeCode}}">{{__('vendorAdmin.name')}}</label>
                                                        <div id="template-{{$localeCode}}"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <nav>
                                            <div class="nav nav-languages" id="nav-tab" role="tablist">
                                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                    <a class="nav-link @if($loop->first) active @endif" id="nav-{{$localeCode}}-tab-short-desc"
                                                       data-toggle="tab" href="#nav-{{$localeCode}}-short-desc" role="tab" aria-controls="nav-{{$localeCode}}-short-desc" aria-selected="false">{{ $properties['native'] }}</a>
                                                @endforeach
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                <div class="tab-pane fade @if($loop->first) show active @endif" id="nav-{{$localeCode}}-short-desc"
                                                     role="tabpanel" aria-labelledby="nav-{{$localeCode}}-tab-short-desc">
                                                    <div class="form-group">
                                                        <label for="short_desc_{{$localeCode}}">{{__('vendorAdmin.short_desc')}}</label>
                                                        <textarea id="short_desc_{{$localeCode}}" class="form-control ckeditor" name="short_desc_{{$localeCode}}">
                                                {{$product->getTranslation('short_desc',$localeCode)}}
                                            </textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <nav>
                                            <div class="nav nav-languages" id="nav-tab" role="tablist">
                                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                    <a class="nav-link @if($loop->first) active @endif" id="nav-{{$localeCode}}-tab-desc"
                                                       data-toggle="tab" href="#nav-{{$localeCode}}-desc" role="tab" aria-controls="nav-{{$localeCode}}-desc" aria-selected="false">{{ $properties['native'] }}</a>
                                                @endforeach
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                <div class="tab-pane fade @if($loop->first) show active @endif" id="nav-{{$localeCode}}-desc"
                                                     role="tabpanel" aria-labelledby="nav-{{$localeCode}}-tab-desc">
                                                    <div class="form-group">
                                                        <label for="desc_{{$localeCode}}">{{__('vendorAdmin.desc')}}</label>
                                                        <textarea id="desc_{{$localeCode}}" rows="5" class="form-control ckeditor" name="desc_{{$localeCode}}">
                                                {{$product->getTranslation('desc',$localeCode)}}
                                            </textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="type">{{__('vendorAdmin.type')}}</label>
                                            <select class="form-control" name="type" id="type">
                                                <option selected disabled>{{__('vendorAdmin.type_placeholder')}}</option>
                                                <option value="product" @if($product->type == 'product') selected @endif>{{__('vendorAdmin.type_product')}}</option>
                                                <option value="service" @if($product->type == 'service') selected @endif>{{__('vendorAdmin.type_service')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cost">{{__('vendorAdmin.cost')}}</label>
                                            <input type="number" class="form-control" name="cost" id="cost" min="0" step="0.01" value="{{$product->cost}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="price">{{__('vendorAdmin.price')}}</label>
                                            <input type="number" class="form-control" name="price" id="price" min="0" step="0.01" value="{{$product->price}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="stock">{{__('vendorAdmin.stock')}}</label>
                                            <input type="number" class="form-control" name="stock" id="stock" min="0" step="1" value="{{$product->stock}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="unit">{{__('vendorAdmin.unit')}}</label>
                                            <select class="form-control" name="unit_id" id="unit">
                                                <option selected disabled>{{__('vendorAdmin.unit_placeholder')}}</option>
                                                @foreach($units as $unit)
                                                    <option value="{{$unit->id}}" @if($product->unit_id == $unit->id) selected @endif>{{$unit->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="min_quantity">{{__('vendorAdmin.min_quantity')}}</label>
                                            <input type="number" class="form-control" name="min_quantity" id="min_quantity" min="1" step="1" value="{{$product->min_quantity}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sku">{{__('vendorAdmin.sku')}}</label>
                                            <input type="text" class="form-control" name="SKU" id="sku" value="{{$product->SKU}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>{{__('vendorAdmin.gallery')}}</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="gallery">{{__('vendorAdmin.gallery')}}</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" multiple name="gallery[]" class="custom-file-input" id="gallery">
                                            <label class="custom-file-label">{{__('vendorAdmin.choose_images')}}</label>
                                        </div>
                                    </div>
                                    <div class="mt-3 temp-img-container"></div>
                                    <div class="mt-3">
                                        <div class="row">
                                        @foreach($product->getMedia(PRODUCT_PATH) as $image)
                                            <div class="col-md-1 image-container">
                                                <a href="{{route('vendor.product.deleteImage', [$product, $image])}}">
                                                    <i class="absolute-image fas fa-times"></i>
                                                </a>
                                                <img class="img-thumbnail" width="100%" src="{{ $image->getUrl('size_width_100') }}">
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>{{__('vendorAdmin.attributes')}}</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <select id="attributes" multiple name="attributes[]">
                                        @foreach($attributes as $attribute)
                                            <optgroup label="{{$attribute->key}}">
                                            @foreach($attribute->values as $value)
                                                <option value="{{$value->id}}" @if(in_array($value->id, $product->attributes->modelKeys())) selected @endif>{{$value->value . ' (' . $attribute->key . ')'}}</option>
                                            @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <h4>{{__('vendorAdmin.shipping')}}</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="shipping">{{__('vendorAdmin.shipping')}}</label>
                                    <select id="shipping" class="form-control" name="shipping_id">
                                        <option value="" selected>{{__('vendorAdmin.shipping_placeholder')}}</option>
                                        @foreach($vendor->shippings as $shipping)
                                            <option value="{{$shipping->id}}" @if($shipping->id == $product->shipping_id) selected @endif>{{$shipping->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="specific">{{__('vendorAdmin.specific_shipping')}}</label>
                                    <input type="checkbox" class="form-control" id="specific" name="specific_shipping" @if($product->specific_shipping) checked @endif>
                                </div>
                                <div class="card" id="specific_shipping">
                                    <div class="card-header card-youmats">
                                        <h3 class="card-title">{{__('vendorAdmin.specific_shipping')}}</h3>
                                    </div>

                                    <div class="card-body">
                                        <label>{{__('vendorAdmin.specific_shipping_terms')}}</label>
                                        <div class="row">
                                            <div class="col-md-12" id="clone-container">
                                            @if($product->shipping_prices)
                                                @foreach($product->shipping_prices as $key => $shipping_price)
                                                    <div class="clone-element" data-iteration="{{$key}}">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <div class="form-group">
                                                                    <label for="car_type">{{__('vendorAdmin.car_type')}}</label>
                                                                    <input type="text" class="form-control" name="cars[{{$key}}][car_type]" id="car_type" value="{{$shipping_price['attributes']['car_type']}}">
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
                                                            @foreach($shipping_price['attributes']['cities'] as $innerKey => $row)
                                                                <div class="row clone-element-cities">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="city">{{__('vendorAdmin.city')}}</label>
                                                                            <select class="form-control" id="city" name="cars[{{$key}}][{{$innerKey}}][city]">
                                                                                <option value="" disabled selected>{{__('vendorAdmin.cities_placeholder')}}</option>
                                                                                @foreach($cities as $city)
                                                                                    <option value="{{$city->id}}" @if($row['city'] == $city->id) selected @endif>{{$city->name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="quantity">{{__('vendorAdmin.quantity')}}</label>
                                                                            <input type="number" class="form-control" id="quantity" name="cars[{{$key}}][{{$innerKey}}][quantity]" min="1" step="1" value="{{$row['quantity']}}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="price">{{__('vendorAdmin.price')}}</label>
                                                                            <input type="number" class="form-control" id="price" name="cars[{{$key}}][{{$innerKey}}][price]" min="0" step="0.05" value="{{$row['price']}}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="time">{{__('vendorAdmin.time')}}</label>
                                                                            <input type="number" class="form-control" id="time" name="cars[{{$key}}][{{$innerKey}}][time]" min="1" step="1" value="{{$row['time']}}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label for="format">{{__('vendorAdmin.format')}}</label>
                                                                            <select class="form-control" id="format" name="cars[{{$key}}][{{$innerKey}}][format]">
                                                                                <option value="" disabled selected>{{__('vendorAdmin.format_placeholder')}}</option>
                                                                                <option value="hour" @if($row['format'] == 'hour') selected @endif>{{__('vendorAdmin.hour')}}</option>
                                                                                <option value="day" @if($row['format'] == 'day') selected @endif>{{__('vendorAdmin.day')}}</option>
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
                                            @endif
                                            </div>
                                            <div class="col-md-12">
                                                <button type="button" id="clone-add" class="btn btn-youmats btn-block">{{__('vendorAdmin.add')}}</button>
                                            </div>
                                        </div>
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
        $(document).ready(function() {
            let main_iteration = {{isset($key) ? $key+1 : 0}},
                inner_iteration = {{isset($innerKey) ? $innerKey+1 : 0}};
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


            @if(!$product->specific_shipping)
                $('#specific_shipping').hide();
            @endif
            $('#specific').on('change', function () {
                $('#specific_shipping').toggle();
            });

            $('#attributes').select2({
                theme: 'classic',
                width: '100%',
                placeholder: "{{__('vendorAdmin.attributes_placeholder')}}"
            });

            getSubCategories({{$selected_category}});
            getTemplateForTitle({{$product->category_id}});

            $('#category').change(function () {
                getSubCategories($(this).val());
            });

            $('#subCategory').change(function () {
                getAttributes($(this).val());
                getTemplateForTitle($(this).val());
            });

            $('.absolute-image').click(function() {
                $(this).parent('.image-container').remove();
            });

            // Multiple images preview in browser
            var imagesPreview = function(input, placeToInsertImagePreview) {
                if (input.files) {
                    var filesAmount = input.files.length;
                    // $('div.temp-img-container').html('');
                    for (i = 0; i < filesAmount; i++) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            $($.parseHTML('<img style="border-color: #F00;" class="img-thumbnail" width="200">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        }
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };
            $('#gallery').on('change', function() {
                imagesPreview(this, 'div.temp-img-container');
            });

        });
        function getSubCategories(category_id) {
            var subCategoryElement = $('#subCategory'),
                selected_subCategory = "{{$product->category_id}}";
            $.ajax({
                type: 'GET',
                url: "{{route('vendor.category.getSub')}}",
                data: { category_id: category_id }
            }).done(function(response) {
                subCategoryElement.html('');
                subCategoryElement.append(`<option value="" selected disabled>{{__('vendorAdmin.subCategory_placeholder')}}</option>`);
                $.each(response, function(key, value){
                    if(key != selected_subCategory) {
                        subCategoryElement.append(`<option value="`+key+`">`+value+`</option>`);
                    } else {
                        subCategoryElement.append(`<option value="`+key+`" selected>`+value+`</option>`);
                    }
                });
            });
        }
        function getAttributes(subCategory_id) {
            var attributes = $('#attributes'),
                locale = "{{\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale()}}";
            $.ajax({
                type: 'GET',
                url: "{{route('vendor.category.getAttr')}}",
                data: { subCategory_id: subCategory_id }
            }).done(function(response){
                attributes.html('');
                $.each(response, function(i, attr){
                    if(locale == 'ar') {
                        attributes.append(`<optgroup label="`+attr.key.ar+`">`);
                        $.each(attr.values, function(i, value) {
                            attributes.append(`<option value="`+value.id+`">`+value.value.ar+`</option>`);
                        });
                        attributes.append(`</optgroup>`);
                    } else if(locale == 'en') {
                        attributes.append(`<optgroup label="`+attr.key.en+`">`);
                        $.each(attr.values, function(i, value) {
                            attributes.append(`<option value="`+value.id+`">`+value.value.en+`</option>`);
                        });
                        attributes.append(`</optgroup>`);
                    }
                });
            });
        }
        function getTemplateForTitle(subCategory_id) {
            $.ajax({
                type: 'GET',
                url: "{{route('vendor.category.getTemplate')}}",
                data: { subCategory_id: subCategory_id }
            }).done(function(response){
                if(response.length == 0 || response[0] == null) {
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        $('#template-{{$localeCode}}').html(`<input type="text" class="form-control" name="name_{{$localeCode}}" id="name-{{$localeCode}}" value="{{$product->getTranslation('name', $localeCode)}}">`);
                    @endforeach
                } else {
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    var template = $('#template-{{$localeCode}}');
                    template.html('');
                    response.forEach(function (value, index) {
                        var word = value.word.{{$localeCode}},
                            firstLetter = word.split('')[0],
                            name = '{{$product->getTranslation('temp_name', $localeCode)}}',
                            splitName = name.split('(^)');
                        if(word == '') return;

                        console.log(name);

                        if(firstLetter == '+') {
                            if(splitName[index] == undefined) {
                                splitName[index] = '';
                            }
                            template.append(`<input type="text" class="form-control d-inline-block w-auto mx-1"
                                    name="name_{{$localeCode}}[`+index+`]" value="`+splitName[index]+`"
                                    placeholder="`+word.substr(1)+`">`
                            );
                        } else if(firstLetter == '-') {
                            var split = word.substr(1).split('-'),
                                options = '';
                            split.splice(1).forEach(function (value) {
                                if(splitName[index] == value) {
                                    options += `<option value="`+value+`" selected>`+value+`</option>`;
                                } else {
                                    options += `<option value="`+value+`">`+value+`</option>`;
                                }
                            });
                            template.append(`<select class="form-control d-inline-block w-auto mx-1" name="name_{{$localeCode}}[`+index+`]">
                                        <option value="" disabled selected>`+split[0]+`</option>`+options+`</select>`);
                        } else {
                            template.append(`
                                <input type="hidden" name="name_{{$localeCode}}[`+index+`]" value="`+word+`" >
                                <label class="mx-1 w-auto">`+word+`</label>
                            `);
                        }
                    });
                    @endforeach
                }
            });
        }
    </script>
@endsection
