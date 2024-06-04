@extends('vendorAdmin.layouts.master')
@section('title')
    <title>{{__('vendorAdmin.edit_branch')}}</title>
@endsection
@section('content')
    <section class="content content-vendor-edit pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <h4>{{__('vendorAdmin.edit_branch')}} ({{$branch->name}})</h4>
                    <form action="{{route('vendor.branch.update', [$branch->id])}}" method="post">
                        <div class="card">
                            {{csrf_field()}}
                            {{method_field('PUT')}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <nav>
                                            <div class="nav nav-languages" id="nav-tab" role="tablist">
                                                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                    <a class="nav-link @if($loop->first) active @endif" id="nav-{{$localeCode}}-tab" data-toggle="tab" href="#nav-{{$localeCode}}" role="tab" aria-controls="nav-{{$localeCode}}" aria-selected="false">{{ $properties['native'] }}</a>
                                                @endforeach
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                                <div class="tab-pane fade @if($loop->first) show active @endif" id="nav-{{$localeCode}}" role="tabpanel" aria-labelledby="nav-{{$localeCode}}-tab">
                                                    <div class="form-group">
                                                        <label for="name-{{$localeCode}}">{{__('vendorAdmin.name')}}</label>
                                                        <input type="text" class="form-control" name="name_{{$localeCode}}"
                                                               id="name-{{$localeCode}}" value="{{$branch->getTranslation('name', $localeCode)}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="city">{{__('vendorAdmin.city')}}</label>
                                            <select class="form-control" name="city_id" id="city">
                                                <option value="" selected disabled>{{__('vendorAdmin.city_placeholder')}}</option>
                                                @foreach($cities as $city)
                                                    <option value="{{$city->id}}" @if($city->id == $branch->city_id) selected @endif>{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="phone">{{__('vendorAdmin.phone')}}</label>
                                            <input type="text" class="form-control" name="phone_number"
                                                   id="phone" value="{{$branch->phone_number}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="fax">{{__('vendorAdmin.fax')}}</label>
                                            <input type="text" class="form-control" name="fax"
                                                   id="fax" value="{{$branch->fax}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="website">{{__('vendorAdmin.website')}}</label>
                                            <input type="text" class="form-control" name="website"
                                                   id="website" value="{{$branch->website}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">{{__('vendorAdmin.address')}}</label>
                                            <input type="text" class="form-control" name="address"
                                                   id="address" value="{{$branch->address}}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="location">{{__('vendorAdmin.location')}}</label>
                                            {!! generate_map() !!}
                                            <input type="hidden" value="{{$branch->latitude}}" class="lat" readonly name="latitude" required>
                                            <input type="hidden" value="{{$branch->longitude}}" class="lng" readonly name="longitude" required>
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
@endsection
