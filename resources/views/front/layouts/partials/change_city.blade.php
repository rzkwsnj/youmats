<div class="modal fade change_city_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('general.select_city_title')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select id="city_select" class="form-control">
                    <option value="" selected disabled>{{__('general.select_city')}}</option>
                    @if(isset($delivery_cities) && count($delivery_cities))
                        @foreach($delivery_cities as $d_city_loop)
                            <option value="{{$d_city_loop->id}}" @if(Session::has('city') && $d_city_loop->id == Session::get('city')) selected @endif>{{$d_city_loop->name}}</option>
                        @endforeach
                    @else
                        @foreach(\App\Models\City::select('id', 'name')->get() as $city_loop)
                            <option value="{{$city_loop->id}}" @if(Session::has('city') && $city_loop->id == Session::get('city')) selected @endif>{{$city_loop->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('general.close')}}</button>
                <button type="button" id="city_submit" class="btn btn-primary" style="border-radius: 25px;margin-top: 0">{{__('general.choose')}}</button>
            </div>
        </div>
    </div>
</div>
@if(isset($ajax))
@section('extraScripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('ready', function () {
            $(document).on('click', '#city_submit', function () {
                $.ajax({
                    type: 'POST',
                    url: "{{route('front.changeCity')}}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        city_id: $('#city_select').val()
                    }
                })
                .done(function(response) {
                    location.reload();
                })
                .fail(function(response) {
                    console.log(response);
                })
            })
        });
    });
</script>
@endsection
@endif
