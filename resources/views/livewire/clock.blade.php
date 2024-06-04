<div>
    <div class="flex mb-4" style="height: 100px;">
        <h3 class="flex text-base text-80 font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"></path>
            </svg>
            <span class="ml-2"> Time Now </span></h3>
        <select
            class="select-box-sm ml-auto min-w-24 h-6 text-xs appearance-none bg-gray-200 px-4 active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline dark:bg-gray-700"
            wire:model.live.debounce.50ms="tz">
            <option value="Asia/Riyadh">Asia/Riyadh</option>
            <option value="Africa/Cairo">Africa/Cairo</option>
            <option value="Asia/Dubai">Asia/Dubai</option>
            <option value="America/New_York">America/New_York</option>
            <option value="Australia/Sydney">Australia/Sydney</option>
            <option value="Europe/Paris">Europe/Paris</option>
            <option value="Europe/London">Europe/London</option>
        </select>
    </div>
    <div class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
        {{--        ٠٧:١٠:٢٦--}}
{{--        <div wire:loading>--:--:--</div>--}}
        <div id="digital-clock" wire:poll.50ms>
            {{ $clock }}
        </div>
    </div>
    <div>
        <span
            class="fi-wi-stats-overview-stat-description text-sm fi-color-custom text-custom-600 dark:text-custom-400 fi-color-info"
            style="--c-400:var(--info-400);--c-600:var(--info-600);">
{{--                       الأحد، يونيو ٢ ٢٠٢٤<--}}
{{--                    <div wire:loading>please wait. . .</div>--}}
                    <div>{{ $date }}</div>
                </span>
    </div>
</div>
