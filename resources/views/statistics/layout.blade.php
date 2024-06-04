<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistics | Youmats Statistics</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{front_url()}}/assets/css/OverlayScrollbars.min.css">
  <!-- leaflet maps -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="{{front_url()}}/assets/css/statistics.min.css" >
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  <style>
    .rtl{
        direction: rtl;
    }
    .align-items-center {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .scrolable_div{

        overflow-y: auto;

        &&::-webkit-scrollbar {
            width: 5px;
          }

        &&::-webkit-scrollbar-thumb {
            background: grey;
            border-radius: 10px;
          }

        &&::-webkit-scrollbar-thumb:hover {
            background: #fff;
          }
    }
    .bg-striped {
        background-image: repeating-linear-gradient( 45deg, rgb(85 60 154 / 20%),
                                                            rgb(85 60 154 / 20%) 50px,
                                                            rgb(179 147 211 / 20%) 50px,
                                                            rgb(179 147 211 / 20%) 100px,
                                                            rgb(48 25 52 / 20%) 100px,
                                                            rgb(48 25 52 / 20%) 150px);
    }
    .faded-bg {
        background-image: repeating-linear-gradient( -45deg, rgb(85 60 154 / 60%),
                                                             rgb(85 60 154 / 60%) 100px,
                                                             rgb(179 147 211 / 60%) 50px,
                                                             rgb(179 147 211 / 60%) 100px,
                                                             rgb(48 25 52 / 60%) 100px,
                                                             rgb(48 25 52 / 60%) 150px );
    }
    .black-text *{
        color: #000 !important;
        font-weight: bold !important;
    }
    .search-input {
        background: grey;
        border: none;
        border-radius: 5px;
        color: #fff;
        &&::placeholder{
            color: #fff;
        }
    }

    .UniqueStatistics, .GeneralStatistics{
        white-space: nowrap;
    }

    }
  </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-footer-fixed sidebar-collapse">

    @include('statistics.layouts.side_bar')
    @yield('content')

<script type="text/javascript" src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.2.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script type="text/javascript" type="text/javascript" src="{{front_url()}}/assets/js/statistics.js"></script></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">

    const GraphOptions = {
        tooltips: {
          displayColors: true,
          callbacks:{
            mode: 'x',
          },
        },
        scales: {
          xAxes: [{
            stacked: false,
            gridLines: {
              display: false,
            },
            ticks: {
                fontColor: "#fff",
                fontSize: 14,
            },
          }],
          yAxes: [{
            stacked: false,
            ticks: {
              beginAtZero: true,
              fontColor: "#fff",
              fontSize: 15,
            },
            type: 'linear',
          }]
        },

         tooltips: {
            enabled: true
         },
        responsive: true,
        maintainAspectRatio: true,
        legend: {
                display: true,
                position: 'top',
                color: '#fff',
                labels: {
                    fontColor: "#fff",
                    fontSize: 18,
                    padding : 15
                }

            },
      }

      const BarOptions = {
        tooltips: {
            displayColors: true,
            callbacks:{
              mode: 'x',
            },
          },
          scales: {
            xAxes: [{
              stacked: true,
              gridLines: {
                display: false,
              },
              ticks: {
                  fontColor: "#fff",
                  fontSize: 14,
              },
            }],
            yAxes: [{
              stacked: true,
              ticks: {
                beginAtZero: true,
                fontColor: "#fff",
                fontSize: 15,
              },
              type: 'linear',
            }]
          },

           tooltips: {
              enabled: true
           },
          responsive: true,
          maintainAspectRatio: true,
          legend: {
                  display: true,
                  position: 'top',
                  color: '#fff',
                  labels: {
                      fontColor: "#fff",
                      fontSize: 18,
                      padding : 15
                  }

              }
      }

    const format = "DD-MM-YYYY HH:mm:ss";

    $(function() {
        $('#reservationtime').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 15,
            locale: {
                format: format
            }
        },
        function(start, end, label) {
            let date = $(".drp-selected").text();
            let replaced = date.split(' - ');
            document.getElementById("date_from").value = replaced[0];
            document.getElementById("date_to").value = replaced[1];
        });
    });
    @if(request()->get('date_from') or request()->get('date_to'))

        $('#reservationtime').daterangepicker({
            timePicker: true,
            startDate: "{{ request()->get('date_from') }}",
            endDate: "{{ request()->get('date_to') }}",
            locale: {
                format: format
            }
        });
    @endif

      function ChangeDisplay() {
        const x = document.getElementsByClassName("GeneralStatistics");
        const y = document.getElementsByClassName("UniqueStatistics");

        var i;
        for (i = 0; i < x.length; i++) {
            (x[i].style.display === "none") ? x[i].style.display = "block" : x[i].style.display = "none";
            (y[i].style.display === "none") ? y[i].style.display = "block" : y[i].style.display = "none";
        }

    };

    function SearchThisTable(search_input_id ,table_id) {
        var filter = document.getElementById(search_input_id).value.toUpperCase();
        var lis = document.getElementById(table_id).getElementsByTagName('li');
        for (var i = 0; i < lis.length; i++) {
            var name = lis[i].innerHTML;

            name.textContent || name.innerText || "";

            if (name.toUpperCase().indexOf(filter) > -1)
                lis[i].style.display = '';
            else
                lis[i].style.display = 'none';
        }

      }

</script>
@yield('extrascripts')

</body>
</html>

