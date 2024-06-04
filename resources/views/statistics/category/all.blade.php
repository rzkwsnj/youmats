@extends('statistics.layout')
@section('content')

<div class="wrapper">

    <div class="content-wrapper">

      <section class="content pt-4">
        <div class="container-fluid">

        @include('statistics.layouts.search_bar')

          <div class="row">
            <div class="col-md-7">

                @include('statistics.layouts.graphs.bars', ['graph_name' => 'ProductsDistributionByCategory', 'unique_graph_name' => 'ProductsDistributionByCategoryUnique', 'graph_title' => 'Top categories by products'])

            </div>
            <div class="col-md-5">

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

  var ProductsDistributionByCategory = document.getElementById("ProductsDistributionByCategory").getContext('2d');
  var DrawProductsDistributionByCategory = new Chart(ProductsDistributionByCategory, {
      type: 'bar',
      data: {
          labels: @php echo json_encode(array_values($top_vendors), JSON_UNESCAPED_UNICODE); @endphp,
          datasets: [

          @php
              $Types_Of_Data = array('visit', 'call', 'chat', 'email');
              $Types_Of_Colors = array( '#0d6efd', '#dc3545', '#198754', '#ffc107');

                  echo "{
                          label: 'Interactions',
                          backgroundColor: '".$Types_Of_Colors[0]."',
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
  let ProductsDistributionByCategoryUnique = document.getElementById("ProductsDistributionByCategoryUnique").getContext('2d');
  let DrewProductsDistributionByCategoryUnique = new Chart(ProductsDistributionByCategoryUnique, {
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
