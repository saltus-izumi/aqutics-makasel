<div class="tw:h-full tw:w-full tw:overflow-auto tw:bg-white">
    @php
        $investmentTableLeftGutter = 20;
        $investmentTableColumnWidths = [
            40, 60, 180, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 200, 60, 220, 60,
        ];
        $investmentTableWidth = $investmentTableLeftGutter + array_sum($investmentTableColumnWidths);
        $investmentTableContentWidth = $investmentTableWidth - $investmentTableLeftGutter;
    @endphp
    <div class="tw:h-[80px] tw:w-full tw:sticky tw:top-0 tw:left-0 tw:bg-white tw:z-[40]">
        <div class="tw:h-[60px] tw:bg-white">
            <x-form.input
                class="tw:!w-[220px]"
                placeholder="物件名（ID）、オーナー名"
                wire:model.live.debounce.300ms="investmentSearchKeyword"
            />
        </div>
        <div
            class="tw:ml-[20px] tw:h-[20px] tw:w-[calc(100%-40px)] tw:flex tw:justify-between tw:bg-white"
            style="max-width: {{ $investmentTableContentWidth }}px;"
        >
            <x-form.checkbox wire:model.live="isManagementActive">管理受託中</x-form.checkbox>
            <div class="tw:flex tw:items-center tw:gap-x-2 tw:text-[0.85rem]">
                <span>1ページ50表示</span>
                @for ($page = 1; $page <= $investments->lastPage(); $page++)
                    <button
                        type="button"
                        @class([
                            'tw:h-[24px] tw:min-w-[24px] tw:px-1 tw:text-center',
                            'tw:bg-[#efefef]' => $page === $investments->currentPage(),
                            'tw:bg-white' => $page !== $investments->currentPage(),
                        ])
                        wire:click="gotoPage({{ $page }})"
                    >
                        {{ $page }}
                    </button>
                @endfor
            </div>
        </div>
    </div>
    <div class="tw:bg-white">
        <table
            class="tw:table-fixed tw:border-separate tw:border-spacing-0"
            style="width: {{ $investmentTableWidth }}px; min-width: {{ $investmentTableWidth }}px;"
        >
            <colgroup>
                <col style="width: {{ $investmentTableLeftGutter }}px;">
                @foreach ($investmentTableColumnWidths as $columnWidth)
                    <col style="width: {{ $columnWidth }}px;">
                @endforeach
            </colgroup>
            <thead class="tw:sticky tw:top-[80px] tw:z-[40]">
                <tr class="tw:h-[40px]">
                    <td class="tw:sticky tw:left-0 tw:z-30 tw:bg-white"></td>
                    <td class="tw:sticky tw:left-[20px] tw:z-20 tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">管理ID</td>
                    <td class="tw:sticky tw:left-[60px] tw:z-20 tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">都市格</td>
                    <td class="tw:sticky tw:left-[120px] tw:z-20 tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">物件名</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">戸数</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">稼働率</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">空室数</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">OPEX</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">PRI<br>㎡単価</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">管理料</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">募集料</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">更新料</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">緊急</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">システム</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">清掃</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">ゴミ</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">保守</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">所在</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">エリア</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">最寄り駅（歩）</td>
                    <td class="tw:text-center tw:bg-[#efefef] tw:border-b tw:border-b-[#cccccc]">契約書</td>
                </tr>
            </thead>
            <tbody>
                @forelse ($investments as $investment)
                    @php
                        $nearestStation = trim(collect([
                            trim((string) $investment->nearest_station_line),
                            trim((string) $investment->nearest_station),
                            filled($investment->nearest_station_walk) ? '歩' . $investment->nearest_station_walk . '分' : '',
                        ])->filter()->implode(' '));
                    @endphp
                    <tr class="tw:h-[40px]">
                        <td class="tw:sticky tw:left-0 tw:z-20 tw:bg-white"></td>
                        <td class="tw:sticky tw:left-[20px] tw:z-10 tw:text-center tw:bg-white tw:border-b tw:border-b-[#cccccc]">{{ $investment->id }}</td>
                        <td class="tw:sticky tw:left-[60px] tw:z-10 tw:text-center tw:truncate tw:bg-white tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1.2rem]"></td>
                        <td class="tw:sticky tw:left-[120px] tw:z-10 tw:truncate tw:bg-white tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1.2rem]">
                            <div class="tw:max-w-full tw:px-[10px] tw:bg-[#ffc5c4] tw:rounded-2xl tw:truncate">{{ $investment->investment_name }}</div>
                        </td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]">{{ $investment->kosu }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1rem]"></td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#ff0000] tw:font-bold tw:text-[1rem]"></td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]"></td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]">{{ filled($investment->pri) ? number_format($investment->pri) : '' }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1rem]">{{ $investment->management_plan }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->advertising_fee }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]"></td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->has_emergency_support ? '有' : '' }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->product_plan }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->cleaning_plan }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->facility_trash_area }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->fixed_repair_plan }}</td>
                        <td class="tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->address }}</td>
                        <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $investment->address_area_id }}</td>
                        <td class="tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">{{ $nearestStation }}</td>
                        <td class="tw:truncate tw:border-b tw:border-b-[#cccccc]">
                            @if (filled($investment->agreement_send))
                                <div class="tw:w-[50px] tw:m-auto tw:text-center tw:border tw:border-[#838383] tw:text-[#a6a6a6]">契約書</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="tw:h-[40px]">
                        <td class="tw:sticky tw:left-0 tw:z-20 tw:bg-white"></td>
                        <td colspan="20" class="tw:text-center tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">該当する物件がありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
