<div>
    <div wire:loading class="absolute inset-0 z-30 flex items-center justify-center rounded-lg bg-white dark:bg-gray-800"
         style="display: none;">
        <svg class="mx-auto block text-gray-300" viewBox="0 0 120 30" xmlns="http://www.w3.org/2000/svg"
             fill="currentColor" style="width: 30px;">
            <circle cx="15" cy="15" r="15">
                <animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear"
                         repeatCount="indefinite"></animate>
                <animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1"
                         calcMode="linear" repeatCount="indefinite"></animate>
            </circle>
            <circle cx="60" cy="15" r="9" fill-opacity="0.3">
                <animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear"
                         repeatCount="indefinite"></animate>
                <animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5"
                         calcMode="linear" repeatCount="indefinite"></animate>
            </circle>
            <circle cx="105" cy="15" r="15">
                <animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear"
                         repeatCount="indefinite"></animate>
                <animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1"
                         calcMode="linear" repeatCount="indefinite"></animate>
            </circle>
        </svg>
    </div>

    <h3 class="h-6 flex mb-8 text-sm font-bold">Users Status <span class="ml-auto font-semibold text-gray-400 text-xs">({{ $totalUsers }} total)</span>
    </h3>

    <div class="flex py-3">
        <div class="w-1/2">
            <ul>
                <li class="text-xs leading-normal">
                    <span class="inline-block rounded-full w-2 h-2 mr-2" style="background-color: rgb(64, 153, 222);"></span>
                    Active <br /> ({{ $totalActiveUser }} - {{ round(($totalActiveUser / $totalUsers) * 100) }}%)
                </li>
            </ul>
        </div>
        <div class="w-1/2">
            @livewire(\App\Filament\Widgets\Embeded\UserStatusChartWidget::class)
        </div>
    </div>
</div>
