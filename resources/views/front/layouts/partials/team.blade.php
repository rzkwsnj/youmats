<!-- Team -->
@if(count($team) > 0)
    <section id="team" class="pb-5">
        <div class="container">
            <div class="d-flex justify-content-between border-bottom border-color-1 flex-lg-nowrap flex-wrap border-md-down-top-0 border-md-down-bottom-0 mb-3 rtl">
                <h3 class="section-title section-title__full mb-0 pb-2 font-size-22">{{__('homee.team_title')}}</h3>
                <a class="d-block text-gray-16" href="{{route('front.team.index')}}">{{__('general.go_to_all_team')}}<i class="ec ec-arrow-right-categproes"></i></a>
            </div>
            <div class="row rtl">
                @foreach($team as $member)
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="image-flip" >
                            <div class="mainflip flip-0">
                                <div class="frontside">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p><img loading="lazy" class="img-fluid" src="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH, 'size_120_120')['url']}}" alt="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH)['alt']}}" title="{{$member->getFirstMediaUrlOrDefault(TEAM_PATH)['title']}}"></p>
                                            <h4 class="card-title">{{$member->name}}</h4>
                                            <p class="card-text">{{$member->position}}</p>
                                            <a href="#" class="btn btn-primary btn-sm icon-plus_team"><i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="backside">
                                    <div class="card">
                                        <div class="card-body text-center mt-4">
                                            <h4 class="card-title">{{$member->name}}</h4>
                                            <p class="card-text">{!! \Str::limit($member->info, 200) !!}</p>
                                            <ul class="list-inline">
                                                @if(isset($member->facebook))
                                                    <li class="list-inline-item">
                                                        <a class="social-icon text-xs-center" target="_blank" href="{{$member->facebook}}">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(isset($member->twitter))
                                                    <li class="list-inline-item">
                                                        <a class="social-icon text-xs-center" target="_blank" href="{{$member->twitter}}">
                                                            <i class="fab fa-twitter"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(isset($member->gmail))
                                                    <li class="list-inline-item">
                                                        <a class="social-icon text-xs-center" target="_blank" href="mailto:{{$member->gmail}}">
                                                            <i class="fab fa-google"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
