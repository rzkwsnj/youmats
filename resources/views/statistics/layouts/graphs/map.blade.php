<div class="card">
    <div class="card-header">
    <h3 class="card-title">{{ $graph_title }}</h3>
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
            <div id="{{ $graph_name }}" style="height: -webkit-fill-available;"></div>
        </div>
        </div>
    </div>
    </div>
</div>
