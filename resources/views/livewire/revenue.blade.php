<div>
    <div class="flex mb-4" style="height: 85px;">
        <h3 class="flex text-base text-80 font-bold">
            Revenue
        </h3>
        <select
            class="select-box-sm ml-auto min-w-24 h-6 text-xs appearance-none bg-gray-200 px-4 active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline dark:bg-gray-700" wire:model.live.debounce.250ms="period">
            <option value="0">7 Days</option>
            <option value="1">Today</option>
            <option value="2">30 Days</option>
            <option value="3">60 Days</option>
            <option value="4">90 Days</option>
        </select>
    </div>
    <div class="flex items-center mb-4 space-x-4">
        <div class="p-3 rounded-lg bg-primary-500 text-white h-14 w-14 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24"
                 height="24" class="inline-block" role="presentation">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <div>
            <div class="fi-wi-stats-overview-stat-value text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                <div wire:loading>-----</div>
                <div wire:loading.remove>{{ $totalRevenue }}</div>
            </div>
            @if($totalRevenue === '0')
                <div class="v-popper--has-tooltip">
                <span class="fi-wi-stats-overview-stat-description text-sm fi-color-custom text-custom-600 dark:text-custom-400 fi-color-info" style="--c-400:var(--info-400);--c-600:var(--info-600);">
                    <div wire:loading.remove>No Data</div>
                </span>
                </div>
            @endif
        </div>
    </div>
</div>

