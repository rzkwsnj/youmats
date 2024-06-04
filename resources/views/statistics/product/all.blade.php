@extends('statistics.layout')
@section('content')

<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

        @include('statistics.layouts.search_bar')

          <div class="row">
            <div class="col-md-7">

                @include('statistics.layouts.graphs.bars', ['graph_name' => 'myChart', 'unique_graph_name' => 'myChartUnique', 'graph_title' => 'Vendors with the most interactions per products'])

            </div>
            <div class="col-md-5">

                @include('statistics.layouts.cards.vendors', ['card_title' => 'Top Vendors', 'vendors' => $top_vendors_list, 'vendors_values' => $top_vendors_values, 'vendor_sub_values' => $top_vendors_sub_values, 'vendor_sub_values_unique' => $top_vendors_sub_values_unique])

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

@endsection
@section('extrascripts')
<script type="text/javascript" defer>

  var ctx = document.getElementById("myChart").getContext('2d');
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: @php echo json_encode(array_values($top_vendors), JSON_UNESCAPED_UNICODE); @endphp,
          datasets: [

          @php
                echo "{
                          label: 'Interactions',
                          backgroundColor: '#0d6efd',
                          data: [";

                          $keys = array_keys($top_vendors_values);
                          for($o=0; $o < count($top_vendors);$o++){
                              if(isset($top_vendors_values[$keys[$o]])){
                                  echo $top_vendors_values[$keys[$o]].",";
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

  var ctxUnique = document.getElementById("myChartUnique").getContext('2d');
  var myChartUnique = new Chart(ctxUnique, {
      type: 'bar',
      data: {
          labels: @php echo json_encode(array_values($top_vendors), JSON_UNESCAPED_UNICODE); @endphp,
          datasets: [

          @php
                echo "{
                          label: 'Interactions',
                          backgroundColor: '#0d6efd',
                          data: [";

                          $keys = array_keys($top_vendors_values_unique);
                          for($o=0; $o < count($top_vendors);$o++){
                              if(isset($top_vendors_values_unique[$keys[$o]])){
                                  echo $top_vendors_values_unique[$keys[$o]].",";
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
</script>

@endsection
