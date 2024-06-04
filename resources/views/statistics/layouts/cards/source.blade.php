<div class="card @if(!count($sources)) collapsed-card @endif">
    <div class="card-header">
      <h3 class="card-title">{{ $card_title }}</h3>
      <div class="card-tools">
        @if(count($sources))
            <input id="categoriesSearch" class="search-input rtl" type="text" onkeyup="SearchThisTable(this.id, 'categoriesTable')" placeholder="البحث ..." >
        @endif
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <div class="card-body p-0">
      <ul id="categoriesTable" class="products-list product-list-in-card scrolable_div pl-2 pr-2" style="height: 385px;">
      @if(count($sources))
          @foreach (array_keys($sources) as $source)
          <li class="item">
            <div class="row" bis_skin_checked="1">
                <div class="col-8 align-items-center" bis_skin_checked="1">
                    <h5 style="text-align: right;">
                        <a href="{{ $source }}" style="color: #ffffff;text-decoration: none;">
                            {{ Str::limit(str_replace(front_url(), '', $source), 30) }}
                        </a>
                    </h5>
                </div>
                <div class="col-4 align-items-center" bis_skin_checked="1">
                    <p style="color:white">
                        {{ $sources[$source] }}<br>
                        <span>interactions</span>
                    </p>
                </div>

            </div>
          </li>

          @endforeach
      @else
        <li>
            <div class="col-12 pt-1">
                <h5 class="align-items-center">No sources To Display</h5>
            </div>
        <li>
      @endif

      </ul>
    </div>
    <div class="card-footer text-center">
      <a href="{{-- route('statistics.log.sources') --}}" class="uppercase">View All sources</a>
    </div>
  </div>
