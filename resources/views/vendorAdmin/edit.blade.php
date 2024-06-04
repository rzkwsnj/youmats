@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_info')}}</title>
@endsection
@section('content')
<section class="content content-vendor-edit pt-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mb-2">
                <h4>{{__('vendorAdmin.edit_info')}}</h4>
                <form action="{{route('vendor.update')}}" method="post" enctype="multipart/form-data">
                    <div class="card">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <nav>
                                        <div class="nav nav-languages" id="nav-tab" role="tablist">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                <a class="nav-link @if($loop->first) active @endif" id="nav-{{$localeCode}}-tab" data-toggle="tab" href="#nav-{{$localeCode}}" role="tab" aria-controls="nav-{{$localeCode}}" aria-selected="false">{{ $properties['native'] }}</a>
                                            @endforeach
                                        </div>
                                    </nav>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <div class="tab-content" id="nav-tabContent">
                                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                            <div class="tab-pane fade @if($loop->first) show active @endif" id="nav-{{$localeCode}}" role="tabpanel" aria-labelledby="nav-{{$localeCode}}-tab">
                                                <div class="form-group">
                                                    <label for="name-{{$localeCode}}">{{__('vendorAdmin.name')}}</label>
                                                    <input type="text" class="form-control" name="name_{{$localeCode}}"
                                                           id="name-{{$localeCode}}" value="{{$vendor->getTranslation('name', $localeCode)}}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <div class="form-group">
                                        <label for="email">{{__('vendorAdmin.main_email')}}</label>
                                        <input type="email" class="form-control" name="email"
                                               id="email" value="{{$vendor->email}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <div class="form-group">
                                        <label for="phone">{{__('vendorAdmin.main_phone')}}</label>
                                        <input type="text" class="form-control" name="phone"
                                               id="phone" value="{{$vendor->phone}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <div class="form-group">
                                        <label for="address">{{__('vendorAdmin.address')}}</label>
                                        <input type="text" class="form-control" name="address"
                                               id="address" value="{{$vendor->address}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xl-12 col-12">
                                    <div class="form-group">
                                        <label for="type">{{__('vendorAdmin.type')}}</label>
                                        <select class="form-control" name="type" id="type">
                                            <option selected disabled>{{__('vendor.type_placeholder')}}</option>
                                            <option value="factory" @if($vendor->type == 'factory') selected @endif>{{__('vendor.type_factory')}}</option>
                                            <option value="distributor" @if($vendor->type == 'distributor') selected @endif>{{__('vendor.type_distributor')}}</option>
                                            <option value="wholesales" @if($vendor->type == 'wholesales') selected @endif>{{__('vendor.type_wholesales')}}</option>
                                            <option value="retail" @if($vendor->type == 'retail') selected @endif>{{__('vendor.type_retail')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.contacts')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" id="clone-container">
                                    @foreach($vendor->contacts as $key => $row)
                                        <div class="clone-element clone-element-add-contact">
                                            <div class="row">
                                                <div class="col-md-4 col-lg-1">
                                                    <div class="form-group">
                                                        <label for="person_name">{{__('vendorAdmin.person_name')}}</label>
                                                        <input type="text" class="form-control" id="person_name" name="contacts_person_name[]" value="{{$row['person_name']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-2">
                                                    <div class="form-group">
                                                        <label for="c_email">{{__('vendorAdmin.email')}}</label>
                                                        <input type="email" class="form-control" id="c_email" name="contacts_email[]" value="{{$row['email']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-2">
                                                    <div class="form-group">
                                                        <label for="c_phone">{{__('vendorAdmin.call_phone')}}</label>
                                                        <input type="text" class="form-control" id="c_call_phone" name="contacts_call_phone[]" value="{{$row['call_phone']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-2">
                                                    <div class="form-group">
                                                        <label for="c_phone">{{__('vendorAdmin.phone')}}</label>
                                                        <input type="text" class="form-control" id="c_phone" name="contacts_phone[]" value="{{$row['phone']}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-5 col-lg-2">
                                                    <div class="form-group">
                                                        <label for="cities">{{__('vendorAdmin.cities')}}</label>
                                                        <select class="form-control select2-cities" data-select2-id="select2-city-{{$loop->index}}" multiple="multiple" id="cities" name="contacts_cities[{{$key}}][]">
                                                            @foreach($cities as $city)
                                                                <option value="{{$city->id}}" @if(in_array($city->id, $row['cities'])) selected @endif>{{$city->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-2">
                                                    <div class="form-group">
                                                        <label for="c_with">{{__('vendorAdmin.with')}}</label>
                                                        <select class="form-control" id="c_with" name="contacts_with[]">
                                                            <option value="" disabled selected>{{__('vendorAdmin.with_placeholder')}}</option>
                                                            <option value="individual" @if($row['with'] == 'individual') selected @endif>{{__('vendorAdmin.individual')}}</option>
                                                            <option value="company" @if($row['with'] == 'company') selected @endif>{{__('vendorAdmin.company')}}</option>
                                                            <option value="both" @if($row['with'] == 'both') selected @endif>{{__('vendorAdmin.both')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-lg-1">
                                                    <div class="form-group">
                                                        <label>{{__('vendorAdmin.remove')}}</label>
                                                        <button class="form-control btn-remove-add btn btn-danger btn-xs clone-remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="clone-add" class="btn btn-youmats btn-block">{{__('vendorAdmin.add')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.gallery')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-12">
                                    <div class="form-group">
                                        <label for="logo">{{__('vendorAdmin.logo')}}</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="logo" class="custom-file-input" id="logo">
                                                <label class="custom-file-label">{{__('vendorAdmin.choose_file')}}</label>
                                            </div>
                                        </div>
                                        <div class="mt-1 mar-left-15">
                                            <img class="img-thumbnail" width="150" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_150_150')['url'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-12">
                                    <div class="form-group">
                                        <label for="cover">{{__('vendorAdmin.cover')}}</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="cover" class="custom-file-input" id="cover">
                                                <label class="custom-file-label">{{__('vendorAdmin.choose_file')}}</label>
                                            </div>
                                        </div>
                                        <div class="mt-1 mar-left-15">
                                            <img class="img-thumbnail" width="150" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_COVER, 'size_height_300')['url'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12 col-12">
                                    <div class="form-group">
                                        <label for="licenses">{{__('vendorAdmin.licenses')}}</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" multiple name="licenses[]" class="custom-file-input" id="licenses">
                                                <label class="custom-file-label">{{__('vendorAdmin.choose_file')}}</label>
                                            </div>
                                        </div>
                                        <div class="mt-1 temp-img-container"></div>
                                        <div class="mt-1 mar-left-15">
                                            @foreach($vendor->getMedia(VENDOR_PATH) as $license)
                                                @if(substr($license->mime_type, 0, 5) == 'image')
                                                    <img class="img-thumbnail" width="200" src="{{ $license->getUrl('licenses') }}">
                                                @else
                                                    <a href="{{ $license->getUrl() }}" target="_blank">
                                                        <img class="img-thumbnail" width="200" src="{{front_url().'/assets/img/default_logo.jpg'}}">
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.location')}}</h4>
                    <div class="card">
                        <div class="card-body">
                           <div class="row">
                               <div class="col-md-12">
                                   <div class="form-group">
                                       <label for="location">{{__('vendorAdmin.location')}}</label>
                                       {!! generate_map() !!}
                                       <input type="hidden" class="lat" value="{{$vendor->latitude}}" readonly name="latitude" required>
                                       <input type="hidden" class="lng" value="{{$vendor->longitude}}" readonly name="longitude" required>
                                   </div>
                               </div>
                           </div>
                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.social_media')}}</h4>
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="facebook">{{__('vendorAdmin.facebook')}}</label>
                                        <input type="url" class="form-control" name="facebook_url"
                                               id="facebook" value="{{$vendor->facebook_url}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="twitter">{{__('vendorAdmin.twitter')}}</label>
                                        <input type="url" class="form-control" name="twitter_url"
                                               id="twitter" value="{{$vendor->twitter_url}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="youtube">{{__('vendorAdmin.youtube')}}</label>
                                        <input type="url" class="form-control" name="youtube_url"
                                               id="youtube" value="{{$vendor->youtube_url}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="instagram">{{__('vendorAdmin.instagram')}}</label>
                                        <input type="url" class="form-control" name="instagram_url"
                                               id="instagram" value="{{$vendor->instagram_url}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="pinterest">{{__('vendorAdmin.pinterest')}}</label>
                                        <input type="url" class="form-control" name="pinterest_url"
                                               id="pinterest" value="{{$vendor->pinterest_url}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="website">{{__('vendorAdmin.website')}}</label>
                                        <input type="url" class="form-control" name="website_url"
                                               id="website" value="{{$vendor->website_url}}">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <h4>{{__('vendorAdmin.password')}}</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="password">{{__('vendorAdmin.password')}}</label>
                                        <input type="password" class="form-control" name="password"
                                               id="password" placeholder="{{__('vendorAdmin.password_leave_it_blank')}}">
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="password_confirmation">{{__('vendorAdmin.password_confirmation')}}</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                               id="password_confirmation">
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
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('FRONT_MAPS_KEY')}}&libraries=places&sensor=false"></script>
    <script src="{{front_url()}}/assets/js/map.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-cities').select2({
                placeholder: "{{__('vendorAdmin.cities_placeholder')}}"
            });
            $('#clone-add').on('click', function () {
                let clone_element = `<div class="clone-element clone-element-add-contact">
                                <div class="row">
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-group">
                                            <label for="person_name">{{__('vendorAdmin.person_name')}}</label>
                                            <input type="text" class="form-control" id="person_name" name="contacts_person_name[]"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <div class="form-group">
                                            <label for="c_email">{{__('vendorAdmin.email')}}</label>
                                            <input type="email" class="form-control" id="c_email" name="contacts_email[]" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <div class="form-group">
                                            <label for="c_phone">{{__('vendorAdmin.phone')}}</label>
                                            <input type="text" class="form-control" id="c_phone" name="contacts_phone[]" />
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-lg-2">
                                        <div class="form-group">
                                            <label for="cities">{{__('vendorAdmin.cities')}}</label>
                                            <select class="form-control select2-cities" multiple="multiple" id="cities" name="contacts_cities[]">
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-2">
                                        <div class="form-group">
                                            <label for="c_with">{{__('vendorAdmin.with')}}</label>
                                                    <select class="form-control" id="c_with" name="contacts_with[]">
                                                        <option value="" disabled selected>{{__('vendorAdmin.with_placeholder')}}</option>
                                                        <option value="individual">{{__('vendorAdmin.individual')}}</option>
                                                        <option value="company">{{__('vendorAdmin.company')}}</option>
                                                        <option value="both">{{__('vendorAdmin.both')}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-lg-1">
                                                <div class="form-group">
                                                    <label>{{__('vendorAdmin.remove')}}</label>
                                                    <button class="form-control btn-remove-add btn btn-danger btn-xs clone-remove">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                $('#clone-container').append(clone_element);
                $('#clone-container').find(".select2-cities").each(function(index) {
                    $(this).attr('data-select2-id', index).select2({
                        placeholder: "{{__('vendorAdmin.cities_placeholder')}}"
                    });
                });
            });
            $(document).on('click', '.clone-remove', function () {
                $(this).closest('.clone-element').remove();
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
            $('#licenses').on('change', function() {
                imagesPreview(this, 'div.temp-img-container');
            });
        });
    </script>
@endsection
