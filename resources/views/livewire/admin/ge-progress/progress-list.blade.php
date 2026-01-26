<div
    class="tw:relative"
    x-data="tablePopup()"
    x-on:click="handleClick($event)"
    x-on:input="handleDateInput($event)"
    x-on:calendar-input.window="handleCalendarInput($event)"
    x-on:keydown.escape.window="close()"
>
    <table class="tw:table-fixed tw:w-[1352px] tw:min-w-[1352px]">
        <colgroup>
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[182px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[182px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
        </colgroup>
        <thead class="tw:sticky tw:top-0">
            <tr class="tw:h-[21px] tw:bg-white">
                <td rowspan="2" colspan="2">
                    <x-button.blue class="tw:!h-[21px] tw:!w-[104px] tw:!font-normal">検索</x-button.blue>
                </td>
                <td class="tw:pl-[10px]" rowspan="2">
                    案件数  ３０
                </td>
                <td rowspan="2" colspan="4"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center" colspan="2">KPI_LT</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">5日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">7日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
            </tr>
            <tr class="tw:h-[50px]">
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">原復ID</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">物件ID</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">物件名</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">号室</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">責任者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">実行者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">ステータス</td>
                <td class="tw:text-center tw:bg-black tw:text-white tw:leading-[1.1rem]">退去<br>受付</td>
                <td class="tw:text-center tw:bg-[#efefef]">解約日</td>
                <td class="tw:text-center tw:bg-[#efefef]">退去日</td>
                <td class="tw:text-center tw:bg-[#efefef]">下代</td>
                <td class="tw:text-center tw:bg-[#efefef]">通電</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">借主<br>負担</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">貸主<br>提案</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">貸主<br>承諾</td>
                <td class="tw:text-center tw:bg-[#efefef]">発注</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>予定</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>受信</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>報告</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">革命<br>控除</td>
                <td class="tw:text-center tw:bg-black tw:text-white">完了</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center" colspan="2">実質LT</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">3日</td>
                <td class="tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">1日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">0日</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">▼</td>
            </tr>
        </thead>
        <tbody>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去受付"
                        data-popup-date="8/31"
                    >8/31</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="解約日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="下代"
                        data-popup-date="10/8"
                    >10/8</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="通電"
                        data-popup-date="10/9"
                    >10/9</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="借主負担"
                        data-popup-date="2025/10/31"
                    >10/31</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:before:content-['.'] tw:before:invisible"
                        data-popup-title="貸主提案"></div>
                </td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去受付"
                        data-popup-date="8/31"
                    >8/31</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="解約日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="下代"
                        data-popup-date="10/8"
                    >10/8</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="通電"
                        data-popup-date="10/9"
                    >10/9</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="借主負担"
                        data-popup-date="10/10"
                    >10/10</div>
                </td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去受付"
                        data-popup-date="8/31"
                    >8/31</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="解約日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="下代"
                        data-popup-date="10/8"
                    >10/8</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="通電"
                        data-popup-date="10/9"
                    >10/9</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="借主負担"
                        data-popup-date="10/10"
                    >10/10</div>
                </td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去受付"
                        data-popup-date="8/31"
                    >8/31</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="解約日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="退去日"
                        data-popup-date="10/7"
                    >10/7</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="下代"
                        data-popup-date="10/8"
                    >10/8</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="通電"
                        data-popup-date="10/9"
                    >10/9</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="借主負担"
                        data-popup-date="10/10"
                    >10/10</div>
                </td>
                <td class="tw:text-center">
                    <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer tw:underline"
                        data-popup-title="貸主提案"
                    ></div>
                </td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                <td class="tw:text-center">1</td>
                <td class="tw:text-center">1</td>
                <td>XXXマンション</td>
                <td class="tw:text-center">101</td>
                <td class="tw:text-center">児玉</td>
                <td class="tw:text-center">脇谷</td>
                <td class="tw:text-center">貸主提案</td>
                <td class="tw:text-center">8/31</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/7</td>
                <td class="tw:text-center">10/8</td>
                <td class="tw:text-center">10/9</td>
                <td class="tw:text-center">10/10</td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
                <td class="tw:text-center"></td>
            </tr>
        </tbody>
    </table>
    <div
        x-show="open"
        x-transition:enter="tw:transition tw:ease-out tw:duration-100"
        x-transition:enter-start="tw:opacity-0 tw:scale-95"
        x-transition:enter-end="tw:opacity-100 tw:scale-100"
        x-transition:leave="tw:transition tw:ease-in tw:duration-75"
        x-transition:leave-start="tw:opacity-100 tw:scale-100"
        x-transition:leave-end="tw:opacity-0 tw:scale-95"
        x-on:click.stop
        class="tw:fixed tw:z-50 tw:max-w-[320px] tw:rounded tw:border tw:border-gray-200 tw:bg-white tw:shadow-lg tw:p-2"
        :style="popupStyle"
        x-cloak
    >
        <div class="tw:text-sm tw:font-semibold tw:text-gray-800 tw:mb-2" x-text="popupTitle"></div>
        <x-form.calendar name="popup_date" />
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('tablePopup', () => ({
                    open: false,
                    popupTitle: '',
                    popupStyle: '',
                    activeTarget: null,
                    calendarName: 'popup_date',

                    handleClick(event) {
                        const trigger = event.target.closest('[data-popup-title]');
                        if (!trigger) {
                            this.close();
                            return;
                        }
                        this.openPopup(trigger, event);
                    },

                    openPopup(trigger, event) {
                        this.activeTarget = trigger;
                        this.popupTitle = trigger.dataset.popupTitle ?? '';
                        const x = event.clientX + 12;
                        const y = event.clientY + 12;
                        this.popupStyle = `left: ${x}px; top: ${y}px;`;
                        this.open = true;
                        this.setCalendarFromTarget(trigger);
                    },

                    handleDateInput(event) {
                        if (!this.open) {
                            return;
                        }
                        const input = event.target;
                        if (!input || !input.matches('[data-calendar-input]')) {
                            return;
                        }
                        this.applyDateToTarget(input.value ?? '');
                    },

                    handleCalendarInput(event) {
                        if (!this.open) {
                            return;
                        }
                        const detail = event?.detail ?? {};
                        if (detail.name && detail.name !== this.calendarName) {
                            return;
                        }
                        this.applyDateToTarget(detail.value ?? '');
                    },

                    setCalendarFromTarget(trigger) {
                        const raw = trigger.dataset.popupDate ?? trigger.textContent ?? '';
                        const value = this.normalizeDate(raw);
                        window.dispatchEvent(new CustomEvent('calendar-set', {
                            detail: { name: this.calendarName, value },
                        }));
                    },

                    applyDateToTarget(value) {
                        if (!this.activeTarget) {
                            return;
                        }
                        const formatted = this.formatMonthDay(value);
                        this.activeTarget.dataset.popupDate = formatted ? value : '';
                        this.activeTarget.textContent = formatted;
                    },

                    close() {
                        this.open = false;
                        this.activeTarget = null;
                    },

                    normalizeDate(value) {
                        const raw = String(value ?? '').trim();
                        if (!raw) {
                            return '';
                        }
                        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                            return raw;
                        }
                        const fullDate = raw.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
                        if (fullDate) {
                            const year = fullDate[1];
                            const month = String(fullDate[2]).padStart(2, '0');
                            const day = String(fullDate[3]).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        }
                        const match = raw.match(/^(\d{1,2})[\/\-](\d{1,2})$/);
                        if (match) {
                            const year = new Date().getFullYear();
                            const month = String(match[1]).padStart(2, '0');
                            const day = String(match[2]).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        }
                        return '';
                    },

                    formatMonthDay(value) {
                        const raw = String(value ?? '').trim();
                        const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                        if (!match) {
                            return '';
                        }
                        return `${Number(match[2])}/${Number(match[3])}`;
                    },
                }));
            });
        </script>
    @endpush
@endonce
