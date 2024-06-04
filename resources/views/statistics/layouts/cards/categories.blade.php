<div class="card @if(!count($categories)) collapsed-card @endif">
    <div class="card-header">
      <h3 class="card-title">{{ $card_title }}</h3>
      <div class="card-tools">
        @if(count($categories))
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
      <ul id="categoriesTable" class="products-list product-list-in-card scrolable_div pl-2 pr-2 rtl" style="height: 385px;">
      @if(count($categories))
          @foreach ($categories as $category)
          <li class="item">
              <div class="row">
                  <div class="col-2">
                      <a href="{{-- route('statistics.log.category',[$category->id]) --}}" target=”_blank”>
                          <img class="img-fluid rounded-pill" width="75" height="75" src="{{ $category->getFirstMediaUrlOrDefault(CATEGORY_PATH, 'size_150_150')['url'] }}" alt="admin">
                      </a>
                  </div>
                  <div class="col-7" >
                      <h5 class="fs-5 mb-1" style="text-align: right;">
                          <a href="{{ route('statistics.log.category', [$category->id]) }}" style="color: #ffffff;text-decoration: none;">
                              {{ Str::limit($category->name, 30) }}
                          </a>
                      </h5>
                      <p class="mb-0" style="text-align: right;">{{ Str::replace(['&nbsp;', '&times;'], [' ', '×'], Str::limit(strip_tags($category->desc), 30)) }}</p>
                  </div>
                  <div class="col-3 align-items-center" >
                      <p style="color:white">
                          {{ $categories_values[$category->id] }}</br>
                          <span>interactions</span>
                      </p>
                  </div>
              </div>
          </li>

          @endforeach
      @else
        <li>
            <div class="col-12 pt-1">
                <h5 class="align-items-center">No Categories To Display</h5>
            </div>
        <li>
      @endif

      </ul>
    </div>
    <div class="card-footer text-center">
      <a href="{{ route('statistics.log.categories') }}" class="uppercase">View All Categories</a>
    </div>
  </div>
