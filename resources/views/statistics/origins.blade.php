@extends('statistics.layout')
@section('content')

<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

            @include('statistics.layouts.search_bar')

          <div class="row">
            <div class="col-md-7">

                @include('statistics.layouts.graphs.map', ['graph_name' => 'MapDiv', 'graph_title' => 'Visitors map'])

            </div>
            <div class="col-md-5">

                @include('statistics.layouts.cards.origins', ['card_title' => 'Top visitors origin', 'origins' => $visitor_origin])

                @include('statistics.layouts.cards.source', ['card_title' => 'Top user sources', 'sources' => $user_agent])

            </div>
          </div>
        </div>
      </section>
    </div>
</div>

@endsection
@section('extrascripts')

<script type="text/javascript" defer>

let locations = [
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

  let map = L.map('MapDiv').setView([24, 45], 5);
  L.tileLayer(
    'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
    }).addTo(map);

  for (let i = 0; i < locations.length; i++) {
    marker = new L.marker([locations[i][1], locations[i][2]])
      .bindPopup(locations[i][0])
      .addTo(map);
  }

</script>
@endsection
