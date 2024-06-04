<div class="card @if(!count($categories)) collapsed-card @endif">
    <div class="card-header">
      <h5 class="card-title">{{ $graph_title }}</h5>
      <div class="card-tools">
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

                <canvas class="GeneralStatistics" id="{{ $graph_name }}" style="height: 180px;"></canvas>
                <canvas class="UniqueStatistics" id="{{ $unique_graph_name }}" style="height: 180px;display: none;"></canvas>

            </div>
          </div>
      </div>
    </div>
</div>
