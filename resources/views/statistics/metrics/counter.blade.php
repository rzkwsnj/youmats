@extends('statistics.layout')
@section('content')

<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-12">
                            <div class="small-bow info-box bg-secondary" style="min-height: 50px;">
                                <form method="get" style="width: inherit;">
                                    <div class="row px-3">
                                        <div class="col-6">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                <input id="reservationtime" type="text" class="form-control float-right"  style="border-radius: 0 5px 5px 0;">
                                                <input id="date_from" type="hidden" name="date_from">
                                                <input id="date_to" type="hidden" name="date_to">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control select2 select2-hidden-accessible" name="country" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                                <option value="All" {{ (request()->get('country') == 'All') ? "selected" :""}} selected="selected">
                                                    Whole World
                                                </option>
                                                <option value="Arab-world" {{ request()->get('country') == 'Arab-world' ? "selected" :""}}>
                                                    Arabe World
                                                </option>
                                                <option value="Saudi-Arabia" {{ request()->get('country') == 'Saudi-Arabia' ? "selected" :""}}>
                                                    Saudi Arabia
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-2 align-items-center">
                                            <div class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-primary px-4" style="font-weight: bolder;" type="submit">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div >
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('statistics.log.counter', ['visit']) }}">
                                    <div class="small-box
                                    @if($page_type == 'visit')
                                        bg-primary
                                    @elseif($page_type == 'call')
                                        bg-danger
                                    @elseif($page_type == 'chat')
                                        bg-success
                                    @elseif($page_type == 'email')
                                        bg-warning
                                    @endif
                                    bg-striped ">
                                        <div class="inner m-0" style="padding: 6px;">
                                            <h3 class="m-0">
                                                {{ $unique[$page_type] }}
                                                <small>
                                                @if($page_type == 'visit')
                                                    Visitors
                                                @elseif($page_type == 'call')
                                                    Unique Call
                                                @elseif($page_type == 'chat')
                                                    Unique Chat
                                                @elseif($page_type == 'email')
                                                    Unique Email
                                                @endif
                                                </small>
                                            </h3>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-eye" style="font-size: 40px;top: 7px;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('statistics.log.counter', ['visit']) }}">
                                    <div class="small-box
                                    @if($page_type == 'visit')
                                        bg-info
                                    @elseif($page_type == 'call')
                                        bg-danger
                                    @elseif($page_type == 'chat')
                                        bg-success
                                    @elseif($page_type == 'email')
                                        bg-warning
                                    @endif
                                    faded-bg ">
                                        <div class="inner" style="padding: 6px;">
                                            <h3 class="m-0">{{ $general[$page_type] }}
                                                <small>
                                                    @if($page_type == 'visit')
                                                        Page Views
                                                    @elseif($page_type == 'call')
                                                        Calls done
                                                    @elseif($page_type == 'chat')
                                                        Chat request
                                                    @elseif($page_type == 'email')
                                                        Email sent
                                                    @endif
                                                </small>
                                            </h3>
                                        </div>
                                        <div class="icon">
                                            <i class="fa-solid fa-binoculars" style="font-size: 40px;top: 7px;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

          <div class="row">
            <div class="col-md-7">
                <div class="card @if(!count($top_categories[$page_type])) collapsed-card @endif">
                    <div class="card-header">
                      <h5 class="card-title">Top visited categories</h5>
                      <div class="card-tools">
                        <span class="badge badge-warning">Top 15 categories</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="chart">
                              <canvas id="myChart" height="180" style="height: 180px;"></canvas>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="card @if(!count($days[$page_type])) collapsed-card @endif">
                    <div class="card-header">
                      <h5 class="card-title">Interactions in the last week</h5>
                      <div class="card-tools">
                        <span class="badge badge-info">Last 7 day</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="chart">
                            <canvas id="myChart2" height="180" style="height: 180px;"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Visitors map</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                        </button>
                    </div>
                    </div>
                    <div class="card-body p-0">
                    <div class="d-md-flex">
                        <div class="flex-fill" style="overflow: hidden">
                        <div id="world-map-markers" style="height: 500px; overflow: hidden">
                            <div id='MapDiv' style="height: -webkit-fill-available;"></div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">

                @include('statistics.layouts.cards.products', ['card_title' => 'Top Products', 'products' => $top_products, 'products_values' => $top_products_values, 'products_sub_values' => $top_products_sub_values])

                @include('statistics.layouts.cards.categories', ['card_title' => 'Top Categories', 'categories' => $categories, 'categories_values' => $categories_values])

                @include('statistics.layouts.cards.vendors', ['card_title' => 'Top Vendors', 'vendors' => $top_vendors, 'vendors_values' => $top_vendors_values])

              </div>
          </div>
        </div>
      </section>
    </div>

  </div>


@endsection
@section('extrascripts')

<script type="text/javascript" defer>

  var ctx2 = document.getElementById("myChart2").getContext('2d');

  var myChart = new Chart(ctx2, {
      type: 'line',
      data: {
          labels:
          @php echo json_encode(array_reverse(array_keys($days[$page_type])), JSON_UNESCAPED_SLASHES); @endphp,
          datasets: [
                @php
                $Types_Of_Colors = array( '#0d6efd', '#dc3545', '#198754', '#ffc107');

                    echo "{
                            label: '".$page_type."',
                            data: ". json_encode(array_reverse(array_values($days[$page_type])), JSON_UNESCAPED_SLASHES) .",
                            fill: false,
                            borderColor: '".$Types_Of_Colors[rand(0,3)]."',
                            backgroundColor: '".$Types_Of_Colors[rand(0,3)]."',
                            borderWidth: 1 // Specify bar border width
                        },";

                @endphp
            ]
      },
      options: {
        ...GraphOptions
      }
  });


var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: @php echo json_encode(array_values($top_categories[$page_type]), JSON_UNESCAPED_SLASHES); @endphp,
		datasets: [

        @php


                echo "{
                        label: '".$page_type."',
                        backgroundColor: '".$Types_Of_Colors[rand(0, 3)]."',
                        data: [";
                        $keys = array_keys($top_categories[$page_type]);
                        for($o=0; $o < count($keys);$o++){
                            if(isset($top_categories_values[$page_type][$keys[$o]])){
                                echo $top_categories_values[$page_type][$keys[$o]].",";
                            }else{
                                echo "0,";
                            }
                        }
                echo "]},";

        @endphp

        ],
	},
    options: {
        ...BarOptions
    }
});


var locations = [
    @php
    if($coordinates){
        for ($i=0; $i < count($coordinates); $i++) {
            $coordinate = explode(",", $coordinates[$i]);
            $lan = preg_replace("/[^0-9.]/", "",$coordinate[0]);
            $lat = preg_replace("/[^0-9.]/", "",$coordinate[1]);
            echo '["LOCATION", '.$lan.','.$lat.'],';
        }
    }else{
        echo '["LOCATION", 0, 0]';
    }
    @endphp
  ];

  var map = L.map('MapDiv').setView([24, 45], 5);
  L.tileLayer(
    'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
    }).addTo(map);

  for (var i = 0; i < locations.length; i++) {
    marker = new L.marker([locations[i][1], locations[i][2]])
      .bindPopup(locations[i][0])
      .addTo(map);
  }

</script>
@endsection
