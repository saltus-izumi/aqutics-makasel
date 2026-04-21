<div class="tw:h-full tw:w-full tw:overflow-auto">
    <div class="tw:h-[120px] tw:w-[286px] tw:px-[26px] tw:pt-[11px]">
        <div class="tw:text-[1.3rem]">
            オーナー選択
        </div>
        <div>
            <x-form.input id="owner-search-input" class="tw:w-[245px] tw:text-[1.2rem]" placeholder="オーナー名（ID）、貸主名" x-ref="searchInput" />
        </div>
        <div class="tw:h-[42px] tw:leading-[42px]">
            <x-form.checkbox id="trading-only" class="tw:text-[1.1rem]" :checked="true">取引中のみ表示</x-form.checkbox>
        </div>
    </div>
    <div class="tw:h-[45px] tw:w-[calc(100%-40px)] tw:ml-[26px] tw:flex tw:items-end tw:border-b tw:mb-[21px]">
        <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#d9d9d9] tw:border-b tw:border-b-3 tw:border-b-pm_blue_001">OWN ID</div>
        <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center tw:bg-[#efefef]">貸主ID</div>
    </div>
    <div class="tw:h-[calc(100%-165px)]">
        <div class="tw:px-[26px]">
            <div>
                <table class="tw:table-fixed tw:w-[1222px] tw:min-w-[1222px]">
                    <colgroup>
                        <col class="tw:w-[52px]">       {{-- ID --}}
                        <col class="tw:w-[156px]">      {{-- OWN名 --}}
                        <col class="tw:w-[234px]">      {{-- 貸主所有名 --}}
                        <col class="tw:w-[78px]">       {{-- 棟数割付シェア --}}
                        <col class="tw:w-[78px]">       {{-- 戸数割付シェア --}}
                        <col class="tw:w-[78px]">       {{-- 売上シェア --}}
                        <col class="tw:w-[182px]">      {{-- 所在地 --}}
                        <col class="tw:w-[182px]">      {{-- スマホ番号 --}}
                        <col class="tw:w-[182px]">      {{-- ログインID（アドレス） --}}
                    </colgroup>
                    <thead class="tw:sticky tw:top-0 tw:z-10">
                        <tr class="tw:h-[42px] tw:bg-white">
                            <td class="tw:pl-[10px] tw:bg-white tw:align-top" colspan="2">
                                <x-button.light-gray class="tw:!h-[28px] tw:!w-[124px] tw:!font-normal">保留</x-button.light-gray>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="tw:h-[50px]">
                            <td class="tw:text-center tw:bg-[#efefef]">ID</td>
                            <td class="tw:text-center tw:bg-[#efefef]">OWN名</td>
                            <td class="tw:text-center tw:bg-[#efefef]">貸主所有名</td>
                            <td class="tw:text-center tw:bg-[#efefef]">棟数割付<br>シェア</td>
                            <td class="tw:text-center tw:bg-[#efefef]">戸数割付<br>シェア</td>
                            <td class="tw:text-center tw:bg-[#efefef]">売上<br>シェア</td>
                            <td class="tw:text-center tw:bg-[#efefef]">所在地</td>
                            <td class="tw:text-center tw:bg-[#efefef]">スマホ番号</td>
                            <td class="tw:text-center tw:bg-[#efefef]">ログインID（アドレス）</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($owners as $owner)
                            <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                                <td class="tw:text-center">{{ $owner->id }}</td>
                                <td>{{ $owner->name }}</td>
                                <td class="tw:truncate">{{ $owner->landlords->pluck('name')->filter()->implode(', ') }}</td>
                                <td class="tw:text-center">80%</td>
                                <td class="tw:text-center">80%</td>
                                <td class="tw:text-center">80%</td>
                                <td class="tw:truncate">{{ $owner->address }}</td>
                                <td>{{ $owner->mobile_phone }}</td>
                                <td>{{ $owner->mail }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
