@extends('statistics.layout')
@section('content')

<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

        @include('statistics.layouts.dashboard_header', ['general' => $general, 'unique' => $unique])

          <div class="row">
            <div class="col-md-7">

                @include('statistics.layouts.graphs.graph', ['graph_name' => 'InteractionsThisPeriod', 'unique_graph_name' => 'InteractionsThisPeriodUnique', 'graph_title' => 'Interactions this Period'])

                @include('statistics.layouts.graphs.bars', ['graph_name' => 'TopVisitedCategories', 'unique_graph_name' => 'TopVisitedCategoriesUnique', 'graph_title' => 'Top visited categories'])

            </div>
            <div class="col-md-5">

                @include('statistics.layouts.cards.vendors', ['card_title' => 'Top Vendors', 'vendors' => $top_vendors, 'vendors_values' => $top_vendors_values, 'vendor_sub_values' => $top_vendors_sub_values, 'vendor_sub_values_unique' => $top_vendors_sub_values_unique])

                @include('statistics.layouts.cards.products', ['card_title' => 'Top Products', 'products' => $top_products, 'products_values' => $top_products_values, 'products_sub_values' => $top_products_sub_values, 'products_sub_values_unique' => $top_products_sub_values_unique])

                @include('statistics.layouts.cards.categories', ['card_title' => 'Top Categories', 'categories' => $categories, 'categories_values' => $categories_values])


            </div>
          </div>
        </div>
      </section>
    </div>

  </div>


@endsection
@section('extrascripts')

<script type="text/javascript" defer>


  let InteractionsThisPeriod = document.getElementById("InteractionsThisPeriod").getContext('2d');
  let InteractionsThisPeriodUnique = document.getElementById("InteractionsThisPeriodUnique").getContext('2d');


  let DrawInteractionsThisPeriod = new Chart(InteractionsThisPeriod, {
      type: 'line',
      data: {
          labels:
          @php echo json_encode(array_reverse(array_keys($days['visit'])), JSON_UNESCAPED_SLASHES); @endphp,
          datasets: [
                @php
                $Types_Of_Data = array('visit', 'call', 'chat', 'email');
                $Types_Of_Colors = array( '#0d6efd', '#dc3545', '#198754', '#ffc107');

                for ($i=0; $i < count($Types_Of_Data); $i++) {
                    echo "{
                            label: '".$Types_Of_Data[$i]."',
                            data: ". json_encode(array_reverse(array_values($days[$Types_Of_Data[$i]])), JSON_UNESCAPED_SLASHES) .",
                            fill: false,
                            borderColor: '".$Types_Of_Colors[$i]."',
                            backgroundColor: '".$Types_Of_Colors[$i]."',
                            borderWidth: 1
                        },";
                }
                @endphp
            ]
      },
      options: {
        ...GraphOptions
      }

  });

  let DrawInteractionsThisPeriodUnique = new Chart(InteractionsThisPeriodUnique, {
    type: 'line',
    data: {
        labels:
        @php echo json_encode(array_reverse(array_keys($days_unique['visit'])), JSON_UNESCAPED_SLASHES); @endphp,
        datasets: [
              @php
              $Types_Of_Data = array('visit', 'call', 'chat', 'email');
              $Types_Of_Colors = array( '#0d6efd', '#dc3545', '#198754', '#ffc107');

              for ($i=0; $i < count($Types_Of_Data); $i++) {
                  echo "{
                          label: '".$Types_Of_Data[$i]."',
                          data: ". json_encode(array_reverse(array_values($days_unique[$Types_Of_Data[$i]])), JSON_UNESCAPED_SLASHES) .",
                          fill: false,
                          borderColor: '".$Types_Of_Colors[$i]."',
                          backgroundColor: '".$Types_Of_Colors[$i]."',
                          borderWidth: 1
                      },";
              }
              @endphp
          ]
    },
    options: {
      ...GraphOptions
    }

});

let TopVisitedCategories = document.getElementById("TopVisitedCategories").getContext('2d');
let DrawTopVisitedCategories = new Chart(TopVisitedCategories, {
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
let TopVisitedCategoriesUnique = document.getElementById("TopVisitedCategoriesUnique").getContext('2d');
let DrewTopVisitedCategoriesUnique = new Chart(TopVisitedCategoriesUnique, {
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

</script>
@endsection
