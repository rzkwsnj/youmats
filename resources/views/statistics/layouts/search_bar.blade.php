<div class="row">
    <div class="col-12">
        <div class="small-bow info-box bg-secondary p-0" style="min-height: 50px;">
            <form method="get" style="width: inherit;">
                <div class="row px-3">
                    <div class="col-6 my-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                            </div>
                            <input id="reservationtime" type="text" class="form-control float-right"  style="border-radius: 0 5px 5px 0;">
                            <input id="date_from" type="hidden" name="date_from">
                            <input id="date_to" type="hidden" name="date_to">
                        </div>
                    </div>
                    <div class="col-3 my-2">
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
                    <div class="col-3 my-1 align-items-center">
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
