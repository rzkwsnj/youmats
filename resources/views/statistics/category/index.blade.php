@extends('statistics.layout')
@section('content')
<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

        @include('statistics.layouts.search_bar')

            <div class="row">
                <div class="col-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile" style="padding: 0.8rem">
                          <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle" src="{{ $category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_200_200')['url'] }}" alt="Category profile picture">
                          </div>

                          <h3 class="profile-username text-center">{{ Str::limit($category->name, 30) }}</h3>
                          <p class="text-muted text-center">{{ Str::limit($category->email, 30) }}</p>
                          <a href="{{route('front.category', [generatedNestedSlug($category->getRelation('ancestors')->pluck('slug')->toArray(), $category->slug)])}}" target="_blank"class="btn btn-primary btn-block">
                            <b>Visit Category</b>
                          </a>
                        </div>
                      </div>
                </div>
                <div class="col-8">
                    <div class="GeneralStatistics" >
                        <div class="row">
                            <div class="col-md-4 col-6">
                                <div class="small-box bg-primary faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $unique['visit'] }}</h3>
                                        <p>Visitors</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="small-box bg-info faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $general['visit'] }}</h3>
                                        <p>Page Views</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-binoculars"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-success faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $general['call'] }}</h3>
                                        <p>Calls done</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-phone-volume"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-danger faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $general['chat'] }}</h3>
                                        <p>Chat request</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-warning faded-bg ">
                                    <div class="inner" style="color: #fff;">
                                        <h3>{{ $general['email'] }}</h3>
                                        <p>Email sent</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>~72% </h3>
                                        <p>Reach rate</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-street-view"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>~65%</h3>
                                        <p>Bounce rate</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-person-circle-question"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="UniqueStatistics" style="display:none;">
                        <div class="row">
                            <div class="col-md-4 col-6">
                                <div class="small-box bg-primary faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $unique['visit'] }}</h3>
                                        <p>Visitors</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="small-box bg-info faded-bg ">
                                    <div class="inner">
                                        @php
                                        if($unique['visit'] > 0){
                                            $avg = number_format((float)$general['visit']/$unique['visit'], 2, '.', '');
                                        }else{
                                            $avg = 0;
                                        }
                                        @endphp
                                        <h3>{{ $avg }}</h3>
                                        <p>Avg pages per visitor</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-binoculars"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-success faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $unique['call'] }}</h3>
                                        <p>Calls done</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-phone-volume"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-danger faded-bg ">
                                    <div class="inner">
                                        <h3>{{ $unique['chat'] }}</h3>
                                        <p>Chat request</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-warning faded-bg ">
                                    <div class="inner" style="color: #fff;">
                                        <h3>{{ $unique['email'] }}</h3>
                                        <p>Email sent</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>~72% </h3>
                                        <p>Reach rate</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-street-view"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>~65%</h3>
                                        <p>Bounce rate</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa-solid fa-person-circle-question"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

          <div class="row">
            <div class="col-md-7">

                @include('statistics.layouts.graphs.graph', ['graph_name' => 'myChart2', 'unique_graph_name' => 'myChart2Unique', 'graph_title' => 'Interactions this Period'])

                @include('statistics.layouts.graphs.bars', ['graph_name' => 'myChart', 'unique_graph_name' => 'myChartUnique', 'graph_title' => 'Top visited Vendors'])

                @include('statistics.layouts.graphs.map', ['graph_name' => 'MapDiv', 'graph_title' => 'Visitors map'])

            </div>
            <div class="col-md-5">

                @include('statistics.layouts.cards.categories', ['card_title' => 'Top Sub-categories', 'categories' => $categories, 'categories_values' => $categories_values])

                @include('statistics.layouts.graphs.doughnut', ['graph_name' => 'donutChart', 'graph_title' => 'Products distribution per Vendors'])

                @include('statistics.layouts.cards.products', ['card_title' => 'Top Products', 'products' => $top_products, 'products_values' => $top_products_values, 'products_sub_values' => $top_products_sub_values])

              </div>
          </div>
        </div>
      </section>
    </div>

  </div>

@endsection
@section('extrascripts')

<script type="text/javascript" defer>

    @php
        $Types_Of_Data = array('visit', 'call', 'chat', 'email');
        $Types_Of_Colors = array( '#0d6efd', '#dc3545', '#198754', '#ffc107');

    @endphp

    var ctx = document.getElementById("donutChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @php echo json_encode(array_values($products), JSON_UNESCAPED_SLASHES); @endphp,
            datasets: [{

                @php echo 'data: '. json_encode(array_values($products_values), JSON_UNESCAPED_SLASHES); @endphp,
            }]},
            options: {
                tooltips: {
                  displayColors: true,
                  callbacks:{
                    mode: 'x',
                  },
                },tooltips: {
                    enabled: true
                 },
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                        display: false
                },
            }
    });


    var ctx2 = document.getElementById("myChart2").getContext('2d');
    var myChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels:
            @php echo json_encode(array_reverse(array_keys($days['visit'])), JSON_UNESCAPED_SLASHES); @endphp,
            datasets: [
                  @php

                  for ($i=0; $i < count($Types_Of_Data); $i++) {
                      echo "{
                              label: '".$Types_Of_Data[$i]."',
                              data: ". json_encode(array_reverse(array_values($days[$Types_Of_Data[$i]])), JSON_UNESCAPED_SLASHES) .",
                              fill: false,
                              borderColor: '".$Types_Of_Colors[$i]."',
                              backgroundColor: '".$Types_Of_Colors[$i]."',
                              borderWidth: 1 // Specify bar border width
                          },";
                  }
                  @endphp
              ]
        },
        options: {
            ...GraphOptions
        }
    });

    var ctx2 = document.getElementById("myChart2Unique").getContext('2d');
    var myChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels:
            @php echo json_encode(array_reverse(array_keys($days_unique['visit'])), JSON_UNESCAPED_SLASHES); @endphp,
            datasets: [
                  @php

                  for ($i=0; $i < count($Types_Of_Data); $i++) {
                      echo "{
                              label: '".$Types_Of_Data[$i]."',
                              data: ". json_encode(array_reverse(array_values($days_unique[$Types_Of_Data[$i]])), JSON_UNESCAPED_SLASHES) .",
                              fill: false,
                              borderColor: '".$Types_Of_Colors[$i]."',
                              backgroundColor: '".$Types_Of_Colors[$i]."',
                              borderWidth: 1 // Specify bar border width
                          },";
                  }
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
          labels: @php echo json_encode(array_values($top_categories['visit']), JSON_UNESCAPED_SLASHES); @endphp,
          datasets: [

          @php

              for ($i=0; $i < count($Types_Of_Data); $i++) {

                  echo "{
                          label: '".$Types_Of_Data[$i]."',
                          backgroundColor: '".$Types_Of_Colors[$i]."',
                          data: [";
                          $keys = array_keys($top_categories['visit']);
                          for($o=0; $o < count($keys);$o++){
                              if(isset($top_categories_values[$Types_Of_Data[$i]][$keys[$o]])){
                                  echo $top_categories_values[$Types_Of_Data[$i]][$keys[$o]].",";
                              }else{
                                  echo "0,";
                              }
                          }
                  echo "]},";
              }

          @endphp

          ],
      },
      options: {
        ...BarOptions
        }
  });
  var myChartUnique = document.getElementById("myChartUnique").getContext('2d');
  var myChart_unique = new Chart(myChartUnique, {
      type: 'bar',
      data: {
          labels: @php echo json_encode(array_values($top_categories_unique['visit']), JSON_UNESCAPED_SLASHES); @endphp,
          datasets: [

          @php

              for ($i=0; $i < count($Types_Of_Data); $i++) {

                  echo "{
                          label: '".$Types_Of_Data[$i]."',
                          backgroundColor: '".$Types_Of_Colors[$i]."',
                          data: [";
                          $keys = array_keys($top_categories_unique['visit']);
                          for($o=0; $o < count($keys);$o++){
                              if(isset($top_categories_values_unique[$Types_Of_Data[$i]][$keys[$o]])){
                                  echo $top_categories_values_unique[$Types_Of_Data[$i]][$keys[$o]].",";
                              }else{
                                  echo "0,";
                              }
                          }
                  echo "]},";
              }

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
