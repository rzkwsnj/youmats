<div class="card @if(!count($vendors)) collapsed-card @endif">
    <div class="card-header">
      <h3 class="card-title">{{ $card_title }}</h3>
      <div class="card-tools">
        @if(count($vendors))
            <input id="vendorsSearch" class="search-input rtl" type="text" onkeyup="SearchThisTable(this.id, 'vendorsTable')" placeholder="البحث ..." >
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
      <ul id="vendorsTable" class="products-list product-list-in-card scrolable_div pl-2 pr-2 rtl" style="height: 480px;">
      @if(count($vendors))
          @foreach ($vendors as $vendor)
          <li class="item @if($vendor->subscribe) bg-warning bg-striped black-text @endif " style="color: #fff !important;">
            <div class="row">
                <div class="col-2">
                    <a href="{{ route('statistics.log.vendor',[$vendor->id]) }}" target=”_blank”>
                        <img class="img-fluid rounded-pill" src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_150_150')['url'] }}" alt="Vendor" width="75" height="75" style="background: #fff;">
                    </a>
                </div>
                <div class="col-7" data-bs-toggle="collapse" href="#collapseExample-{{ $vendor->id }}" role="button" aria-expanded="false" aria-bs-controls="collapseExample-{{ $vendor->id }}">
                    <h5 class="fs-5 mb-1" style="text-align: right;">
                        <a href="{{ route('statistics.log.vendor', [$vendor->id]) }}" style="color: #ffffff;text-decoration: none;">
                            {{ Str::limit($vendor->name, 30) }}
                        </a>
                        <a href="{{ route('vendor.show', [$vendor->slug]) }}" target=”_blank”>
                          <i class="fa fa-external-link"></i>
                        </a>
                    </h5>
                    <p class="mb-0" style="text-align: right;">{{ Str::replace(['&nbsp;', '&times;'], [' ', '×'], Str::limit(strip_tags($vendor->email), 30)) }}</p>
                </div>
                <div class="col-3 align-items-center" >
                    <p style="color:white">
                        {{ $vendors_values[$vendor->id] }}</br>
                        <span>interactions</span>
                    </p>
                </div>
            </div>
            <div class="collapse" id="collapseExample-{{ $vendor->id }}" style="background: rgb(124 124 124 / 35%);padding: 1rem 8rem !important;">
                <div class="GeneralStatistics">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values['visit'][$vendor->id]) ? $vendor_sub_values['visit'][$vendor->id] : '0' }}
                            </h3>
                            <p class="m-0 lead">View</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values['call'][$vendor->id]) ? $vendor_sub_values['call'][$vendor->id] : '0' }}
                            </h3>
                            <p class="m-0 lead">Call</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values['chat'][$vendor->id]) ? $vendor_sub_values['chat'][$vendor->id] : '0' }}
                            </h3>
                            <p class="m-0 lead">Chat</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values['email'][$vendor->id]) ? $vendor_sub_values['email'][$vendor->id] : '0' }}
                            </h3>
                            <p class="m-0 lead">Email</p>
                        </div>
                    </div>
                </div>
                <div class="UniqueStatistics" style="display: none;">
                    <div class="row align-items-center ">
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values_unique['visit'][$vendor->id]) ? $vendor_sub_values_unique['visit'][$vendor->id] : '-' }}
                            </h3>
                            <p class="m-0 lead">visits</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values_unique['call'][$vendor->id]) ? $vendor_sub_values_unique['call'][$vendor->id] : '-' }}
                            </h3>
                            <p class="m-0 lead">Call</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values_unique['chat'][$vendor->id]) ? $vendor_sub_values_unique['chat'][$vendor->id] : '-' }}
                            </h3>
                            <p class="m-0 lead">Chat</p>
                        </div>
                        <div class="col-3">
                            <h3>
                                {{ !empty($vendor_sub_values_unique['email'][$vendor->id]) ? $vendor_sub_values_unique['email'][$vendor->id] : '-' }}
                            </h3>
                            <p class="m-0 lead">Email</p>
                        </div>
                    </div>
                </div>
            </div>
        </li>

          @endforeach
      @else
      <li>
        <div class="col-12 pt-1">
            <h5 class="align-items-center">No Vendors To Display</h5>
        </div>
    <li>
      @endif

      </ul>
    </div>

    <div class="card-footer text-center">
        <a href="{{ route('statistics.log.vendors') }}">View All Vendors</a>
    </div>
</div>
