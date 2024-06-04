<div class="card @if(!count($products)) collapsed-card @endif">
    <div class="card-header">
      <h3 class="card-title">{{ $card_title }}</h3>
      <div class="card-tools">
        @if(count($products))
            <input id="productsSearch" class="search-input rtl" type="text" onkeyup="SearchThisTable(this.id, 'ProductsTable')" placeholder="البحث ..." >
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

      <ul id="ProductsTable" class="products-list product-list-in-card scrolable_div pl-2 pr-2 rtl" style="height: 480px;">

      @if(count($products))
          @foreach ($products as $product)
          <li class="item @if($product->vendor->subscribe) bg-warning faded-bg black-text @endif " style="color: #fff !important;">
              <div class="row">
                  <div class="col-2">
                      <a href="{{-- route('statistics.log.product',[$product->id]) --}}" target=”_blank”>
                          <img class="img-fluid rounded-pill" width="75" height="75" src="{{ $product->getFirstMediaUrlOrDefault(PRODUCT_PATH, 'size_150_150')['url'] }}" alt="admin">
                      </a>
                  </div>
                  <div class="col-7" data-bs-toggle="collapse" href="#collapseExample-{{ $product->id }}" role="button" aria-expanded="false" aria-bs-controls="collapseExample-{{ $product->id }}">
                      <h5 class="fs-5 mb-1" style="text-align: right;">
                          <a style="color: #ffffff;text-decoration: none;">
                              {{ Str::limit($product->name, 30) }}
                          </a>
                          <a href="{{route('front.product', [generatedNestedSlug($product->category->ancestors->pluck('slug')->toArray(), $product->category->slug), $product->slug])}}">
                            <i class="fa fa-external-link"></i>
                          </a>
                      </h5>
                      <p class="mb-0" style="text-align: right;">
                        {{ Str::replace(['&nbsp;', '&times;'], [' ', '×'], Str::limit(strip_tags($product->vendor->name), 30)) }}
                      </p>
                  </div>
                  <div class="col-3 align-items-center" >
                      <p style="color:white">
                          {{ $products_values[$product->id] }}</br>
                          <span>interactions</span>
                      </p>
                  </div>
              </div>
              <div class="collapse" id="collapseExample-{{ $product->id }}" style="background: rgb(124 124 124 / 35%);padding: 1rem 8rem !important;">
                    <div class="GeneralStatistics">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values['visit'][$product->id]) ? $products_sub_values['visit'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">View</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values['call'][$product->id]) ? $products_sub_values['call'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Call</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values['chat'][$product->id]) ? $products_sub_values['chat'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Chat</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values['email'][$product->id]) ? $products_sub_values['email'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Email</p>
                            </div>
                        </div>
                    </div>
                    <div class="UniqueStatistics" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values_unique['visit'][$product->id]) ? $products_sub_values_unique['visit'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">View</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values_unique['call'][$product->id]) ? $products_sub_values_unique['call'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Call</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values_unique['chat'][$product->id]) ? $products_sub_values_unique['chat'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Chat</p>
                            </div>
                            <div class="col-3">
                                <h3>
                                    {{ !empty($products_sub_values_unique['email'][$product->id]) ? $products_sub_values_unique['email'][$product->id] : '0' }}
                                </h3>
                                <p class="m-0 lead">Email</p>
                            </div>
                        </div>
                    </div>
              </div>
          </li>

          @endforeach
      @else
              <li class="filterable-cell">
                  <div class="admin d-flex rounded-2 p-2 mb-2 mx-0 row">
                      <div class="col-12 pt-1">
                          <h5 class="align-items-center">No Products To Display</h5>
                      </div>
                  </div>
              </li>
      @endif

      </ul>
    </div>

    <div class="card-footer text-center">
      <a href="{{ route('statistics.log.products') }}" class="uppercase">View All Products</a>
    </div>
  </div>
