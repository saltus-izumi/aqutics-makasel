<div class="tw:flex tw:gap-x-[104px] tw:border-b">
    <div class="tw:w-[832px]">
        <div class="tw:w-full tw:h-[42px] tw:flex tw:gap-x-[1.5rem] tw:items-center">
            <div class="tw:text-[1.2rem]">原復ID：{{ $progress->id }}</div>
            <div class="tw:text-[1.2rem]">物件ID：{{ $progress->investment_id }}</div>
            <div class="tw:max-w-[340px] tw:text-[1.2rem] tw:truncate">工事会社：{{ $progress->investment?->restorationCompany?->name }}（担当：{{ $progress->investment?->restorationCompany?->personnel1 }}）</div>
            <div class="tw:text-[1.2rem]">所有者：{{ $progress->genpukuResponsible?->user_name }}</div>
            <div class="tw:text-[1.2rem]">実行担当：{{ $progress->geProgress?->executorUser?->user_name }}</div>
        </div>
        <div class="tw:h-[42px] tw:mb-[21px] tw:leading-[42px] tw:text-[2.6rem] tw:font-bold">
            {{ $progress->investment->investment_name }}　{{ $progress->investmentRoom->investment_room_number }}（{{ $progress->investmentRoomResidentHisotry?->contractor_name }}さま）
        </div>
        <div class="tw:h-[42px] tw:flex">
            <a href="{{ route('admin.progress.ge.detail', ['progressId' => $progress->id]) }}">
                <div @class([
                    'tw:w-[130px] tw:h-full tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center',
                    'tw:bg-[#cccccc] tw:border-b-4 tw:border-[#1155cc]' => ($mode == 'move-out-settlement'),
                    'tw:bg-[#efefef]' => ($mode == 'owner-settlement'),
                ])>
                    退去精算
                </div>
            </a>
            <a href="{{ route('admin.progress.ge.owner-settlement', ['progressId' => $progress->id]) }}">
                <div @class([
                    'tw:w-[130px] tw:h-full tw:leading-[42px] tw:text-[1.4rem] tw:font-bold tw:text-center',
                    'tw:bg-[#cccccc] tw:border-b-4 tw:border-[#1155cc]' => ($mode == 'owner-settlement'),
                    'tw:bg-[#efefef]' => ($mode == 'move-out-settlement'),
                ])>
                    貸主精算
                </div>
            </a>
        </div>
    </div>
    <div>
        <div class="tw:mb-[21px]">
            <table>
                <tr class="tw:h-[42px]">
                    <td class="tw:w-[182px] tw:text-[1.3rem] tw:bg-[#efefef] tw:text-center tw:border tw:border-[#cccccc]">
                        ネクストアクション
                    </td>
                    <td class="tw:w-[312px] tw:pl-[1rem] tw:text-[1.5rem] tw:border tw:border-[#cccccc]">
                        {{ App\Models\GeProgress::NEXT_ACTIONS[$progress->geProgress?->next_action] ?? '' }}
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <table class="tw:w-[780px] tw:table-fixed">
                <tr class="tw:h-[21px]">
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]" rowspan="2">
                        LT
                    </td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                        退去〜下代
                    </td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                        下代〜提案
                    </td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                        提案〜承諾
                    </td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                        承諾〜発注
                    </td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">
                        発注〜完工受信
                    </td>
                </tr>
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['genpuku_mitsumori_recieved'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['genpuku_teian_date'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['genpuku_teian_kyodaku'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['genpuku_kouji_hachu'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['kanko_jyushin_date'] }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
