<div>
    <h3 class="h-6 flex mb-8 text-sm font-bold">Users Type <span class="ml-auto font-semibold text-gray-400 text-xs">({{ $totalUsers }} total)</span>
    </h3>

    <div class="flex py-3">
        <div class="w-1/2">
            <ul>
                <li class="text-xs leading-normal">
                    <span class="inline-block rounded-full w-2 h-2 mr-2" style="background-color: rgb(64, 153, 222);"></span>individual <br /> ({{ $totalIndividualUser }} - {{ round(($totalIndividualUser / $totalUsers) * 100) }}%)
                </li>
                <li class="mt-2 text-xs leading-normal"><span class="inline-block rounded-full w-2 h-2 mr-2" style="background-color: rgb(249, 144, 55);"></span>company
                    <br /> ({{ $totalCompanyUser }} - {{ round(($totalCompanyUser / $totalUsers) * 100) }}%)
                </li>
            </ul>
        </div>
        <div class="w-1/2">
            @livewire(\App\Filament\Widgets\Embeded\UserTypeChartWidget::class)
        </div>
    </div>
</div>
