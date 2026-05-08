<div class="tw:h-full tw:w-full tw:overflow-auto tw:bg-white">
    <div class="tw:h-[60px] tw:w-full tw:sticky tw:top-0 tw:left-0 tw:bg-white tw:z-[40]">
        <div class="tw:h-[60px] tw:sticky tw:top-0 tw:bg-white tw:z-[40]">
            <x-form.input
                class="tw:!w-[220px]"
                placeholder="物件名（ID）、オーナー名"
                wire:model.live.debounce.300ms="investmentSearchKeyword"
            />
        </div>
    </div>
    <div class="tw:h-[20px] tw:w-[calc(100vw-360px)] tw:sticky tw:left-[20px] tw:flex tw:justify-between">
        <x-form.checkbox>管理受託中</x-form.checkbox>
        <div>1ページ50表示 1 2 3 4 5 6 7 8</div>
    </div>
    <div class="tw:bg-white">
        @php
            $investmentTableLeftGutter = 20;
            $investmentTableColumnWidths = [
                40, 60, 180, 60, 60, 60, 60, 60, 60, 60,
                60, 60, 60, 60, 60, 60, 200, 60, 220, 60,
            ];
            $investmentTableWidth = $investmentTableLeftGutter + array_sum($investmentTableColumnWidths);
        @endphp
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
            <thead class="tw:sticky tw:top-[80px] tw:z-10">
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
                <tr class="tw:h-[40px]">
                    <td class="tw:sticky tw:left-0 tw:z-20 tw:bg-white"></td>
                    <td class="tw:sticky tw:left-[20px] tw:z-10 tw:text-center tw:bg-white tw:border-b tw:border-b-[#cccccc]">1</td>
                    <td class="tw:sticky tw:left-[60px] tw:z-10 tw:text-center tw:truncate tw:bg-white tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1.2rem]">A</td>
                    <td class="tw:sticky tw:left-[120px] tw:z-10 tw:truncate tw:bg-white tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1.2rem]">
                        <div class="tw:max-w-full tw:px-[10px] tw:bg-[#ffc5c4] tw:rounded-2xl tw:truncate">XXXXXXX物件XXXXXXX物件</div>
                    </td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]">30</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1rem]">95%</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#ff0000] tw:font-bold tw:text-[1rem]">2</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]">15.00%</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#7f7f7f] tw:font-bold tw:text-[1rem]">3,500</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#2283C8] tw:font-bold tw:text-[1rem]">5%</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">50%</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">50%</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">200</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">1,800</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">15,000</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">1,000</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">20,000</td>
                    <td class="tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">XXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
                    <td class="tw:text-center tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">城南</td>
                    <td class="tw:truncate tw:border-b tw:border-b-[#cccccc] tw:text-[#a6a6a6]">東急 田園都市線 〇〇駅 歩5分</td>
                    <td class="tw:truncate tw:border-b tw:border-b-[#cccccc]">
                        <div class="tw:w-[50px] tw:m-auto tw:text-center tw:border tw:border-[#838383] tw:text-[#a6a6a6]">契約書</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
