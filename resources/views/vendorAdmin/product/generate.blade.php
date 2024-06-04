@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.generate_product')}}</title>
@endsection
@section('content')
    <style>
        .form-control-custom {
            width: 100% !important;
            margin-bottom: 0 !important;
        }
        .form-group-custom {
            margin-bottom: 10px !important;
        }
    </style>
    <section class="content content-vendor-edit pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.generate_product')}}</h4>
                    <form action="{{route('vendor.product.request.generate')}}" method="post" enctype="multipart/form-data">
                        <div class="card">
                            {{csrf_field()}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="category">{{__('vendorAdmin.category')}}</label>
                                            <select class="form-control" id="category">
                                                <option value="" selected disabled>{{__('vendorAdmin.category_placeholder')}}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label for="subCategory">{{__('vendorAdmin.subCategory')}}</label>
                                            <select class="form-control" name="category_id" id="subCategory">
                                                <option value="" selected disabled>{{__('vendorAdmin.subCategory_placeholder')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="template-container" style="display: none">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label>{{__('vendorAdmin.template')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div id="template-ar"></div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div id="template-en"></div>
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
            $('#category').change(function () {
                getSubCategories($(this).val());
            });
            $('#subCategory').change(function () {
                getTemplateForTitle($(this).val());
            });
        });
        function getSubCategories(category_id) {
            var subCategoryElement = $('#subCategory');
            $.ajax({
                type: 'GET',
                url: "{{route('vendor.category.getSub', [true])}}",
                data: { category_id: category_id }
            }).done(function(response) {
                subCategoryElement.html('');
                subCategoryElement.append(`<option value="" selected disabled>{{__('vendorAdmin.subCategory_placeholder')}}</option>`);
                $.each(response, function(key, value){
                    subCategoryElement.append(`<option value="`+key+`">`+value+`</option>`);
                });
            });
        }
        function getTemplateForTitle(subCategory_id) {
            $.ajax({
                type: 'GET',
                url: "{{route('vendor.category.getTemplate')}}",
                data: { subCategory_id: subCategory_id }
            }).done(function(response){
                if(response.length != 0 && response[0] != null) {
                    $('#template-container').show();
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        var template = $('#template-{{$localeCode}}');
                        template.html('');
                        response.forEach(function (value, index) {
                            let word = value.word.{{$localeCode}},
                                firstLetter = word.split('')[0];
                            if(firstLetter == '+') {
                                template.append(`<div class="form-group form-group-custom"><input type="text" class="form-control form-control-custom" name="template_{{$localeCode}}[`+index+`]"
                                            placeholder="`+word.substr(1)+`"></div>`);
                            } else if(firstLetter == '-') {
                                let split = word.substr(1).split('-'),
                                    options = '';
                                split.splice(1).forEach(function (value) {
                                    options += `<option value="`+value+`">`+value+`</option>`;
                                });
                                template.append(`<div class="form-group form-group-custom">
                                            <select id="select2-{{$localeCode}}-`+index+`" data-select2-id="{{$localeCode}}-`+index+`" multiple class="form-control form-control-custom"
                                                    name="template_{{$localeCode}}[`+index+`][]">`+options+`</select></div>`);
                                $('#select2-{{$localeCode}}-'+index).select2({theme: 'classic', width: '100%', placeholder: split[0]});
                            } else if(firstLetter) {
                                template.append(`<div class="form-group form-group-custom"><input class="form-control form-control-custom" type="text" readonly
                                            name="template_{{$localeCode}}[`+index+`]" value="`+word+`"></div>`);
                            }
                        });
                    @endforeach
                }
            });
        }
    </script>
@endsection
