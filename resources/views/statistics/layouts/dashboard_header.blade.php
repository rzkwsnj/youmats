<div class="row">
    <div class="col-lg-6">
        <div class="row">
            <div class="col-12">
                <div class="small-bow info-box bg-secondary" style="min-height: 70px;">
                    <form method="get" style="width: inherit;">
                        <div class="row px-3">
                            <div class="col-7 my-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <input id="reservationtime" type="text" class="form-control float-right" style="border-radius: 0 5px 5px 0;">
                                    <input id="date_from" type="hidden" name="date_from" value="{{ request()->get('date_from') }}">
                                    <input id="date_to" type="hidden" name="date_to" value="{{ request()->get('date_to') }}">
                                </div>
                            </div>
                            <div class="col-5 my-2">
                                <select class="form-control select2 select2-hidden-accessible" name="country" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                    <option value="All" {{ (request()->get('country') == 'All') ? "selected" :""}} selected="selected">
                                        Whole World
                                    </option>
                                    <option value="Arab-world" {{ request()->get('country') == 'Arab-world' ? "selected" :""}}>
                                        Arabe World
                                    </option>
                                    <option value="Saudi-Arabia" {{ request()->get('country') == 'Saudi-Arabia' ? "selected" :""}}>
                                        Saudi Arabia
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-1 align-items-center">
                                <div class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary px-4" style="font-weight: bolder;" type="submit">Filter</button>
                                        <a class="btn btn-success px-4" style="font-weight: bolder;" onclick="ChangeDisplay()">Unique</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>~72% </h3>
                        <p>Reach rate</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-street-view"></i>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>~65%</h3>
                        <p>Bounce rate</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-person-circle-question"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="GeneralStatistics" >
            <div class="row">
                <div class="col-6">
                    <a href="{{ route('statistics.log.counter', ['visit']) }}">
                        <div class="small-box bg-primary faded-bg ">
                            <div class="inner">
                                <h3>{{ $unique['visit'] }}</h3>
                                <p>Visitors</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('statistics.log.counter', ['visit']) }}">
                        <div class="small-box bg-info faded-bg ">
                            <div class="inner">
                                <h3>{{ $general['visit'] }}</h3>
                                <p>Page Views</p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-binoculars"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-6">
                    <a href="{{ route('statistics.log.counter', ['chat']) }}">
                        <div class="small-box bg-success faded-bg ">
                            <div class="inner">
                                <h3>{{ $general['chat'] }}</h3>
                                <p>Chat request</p>
                            </div>
                            <div class="icon">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-6">
                    <a href="{{ route('statistics.log.counter', ['call']) }}">
                        <div class="small-box bg-danger faded-bg ">
                            <div class="inner">
                                <h3>{{ $general['call'] }}</h3>
                                <p>Calls done</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-phone-volume"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="{{ route('statistics.log.counter', ['email']) }}">
                        <div class="small-box bg-warning faded-bg ">
                            <div class="inner" style="color: #fff;">
                                <h3>{{ $general['email'] }}</h3>
                                <p>Email sent</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <div class="UniqueStatistics" style="display: none;">
            <div class="row">
                <div class="col-6">
                    <a href="{{ route('statistics.log.counter', ['visit']) }}">
                        <div class="small-box bg-primary bg-striped">
                            <div class="inner">
                                <h3>{{ $unique['visit'] }}</h3>
                                <p>Visitors</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('statistics.log.counter', ['visit']) }}">
                        <div class="small-box bg-info bg-striped">
                            <div class="inner">
                                @php
                                if($unique['visit'] > 0){
                                    $avg = number_format((float)$general['visit']/$unique['visit'], 2, '.', '');
                                }else{
                                    $avg = 0;
                                }
                                @endphp
                                <h3>{{ $avg }}</h3>
                                <p>Avg pages per visitor</p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-binoculars"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-6">
                    <a href="{{ route('statistics.log.counter', ['chat']) }}">
                        <div class="small-box bg-success bg-striped">
                            <div class="inner">
                                <h3>{{ $unique['chat'] }}</h3>
                                <p>Chat request</p>
                            </div>
                            <div class="icon">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-6">
                    <a href="{{ route('statistics.log.counter', ['call']) }}">
                        <div class="small-box bg-danger bg-striped">
                            <div class="inner">
                                <h3>{{ $unique['call'] }}</h3>
                                <p>Calls done</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-phone-volume"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-6">
                    <a href="{{ route('statistics.log.counter', ['email']) }}">
                        <div class="small-box bg-warning bg-striped">
                            <div class="inner" style="color: #fff;">
                                <h3>{{ $unique['email'] }}</h3>
                                <p>Email sent</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
