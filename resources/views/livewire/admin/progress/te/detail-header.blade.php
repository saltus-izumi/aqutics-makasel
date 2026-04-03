<div class="tw:flex tw:gap-x-[104px] tw:border-b">
    <div class="tw:w-[832px]">
        <div class="tw:w-full tw:h-[42px] tw:flex tw:gap-x-[1.5rem] tw:items-center">
            <div class="tw:text-[1.2rem]">TEID：{{ $teProgress->id }}</div>
            <div class="tw:text-[1.2rem]">物件ID：{{ $teProgress?->investment_id }}</div>
            <div class="tw:text-[1.2rem]">所有者：{{ $teProgress?->responsibleUser?->user_name }}</div>
            <div class="tw:text-[1.2rem]">実行担当：{{ $teProgress?->executorUser?->user_name }}</div>
        </div>
        <div class="tw:h-[42px] tw:mb-[21px] tw:leading-[42px] tw:text-[2.6rem] tw:font-bold">
            {{ $teProgress?->investment?->investment_name }}　{{ $teProgress?->investmentRoom?->investment_room_number ?: '共用部' }}（{{ $teProgress?->investmentRoomResidentHistory?->contractor_name }}さま）
        </div>
    </div>
    <div>
        <div class="tw:mb-[21px] tw:flex tw:items-center tw:gap-x-[26px]">
            @php
                $isReProposeOrCancelDisabled = in_array($teProgress?->next_action, [
                    App\Models\TeProgress::NEXT_ACTION_RE_PROPOSED,
                    App\Models\TeProgress::NEXT_ACTION_CANCEL,
                ], true);
            @endphp
            <table>
                <tr class="tw:h-[42px]">
                    <td class="tw:w-[182px] tw:text-[1.3rem] tw:bg-[#efefef] tw:text-center tw:border tw:border-[#cccccc]">
                        ネクストアクション
                    </td>
                    <td class="tw:w-[208px] tw:pl-[1rem] tw:text-[1.5rem] tw:border tw:border-[#cccccc]">
                        {{ App\Models\TeProgress::NEXT_ACTIONS[$teProgress?->next_action] ?? '' }}
                    </td>
                </tr>
            </table>
            <div>
                <x-button.blue
                    class="tw:!h-[31px] tw:!w-[156px] tw:!rounded-lg tw:text-[1.2rem]"
                    type="button"
                    :disabled="$isReProposeOrCancelDisabled"
                    x-on:click="if (!confirm('再提案処理を実施します。よろしいですか。\nこの処理は取り消しできません。')) { return; } $wire.rePropose();"
                >
                    再提案
                </x-button.blue>
            </div>
            <div>
                <x-button.red
                    class="tw:!h-[31px] tw:!w-[156px] tw:!rounded-lg tw:text-[1.2rem]"
                    type="button"
                    :disabled="$isReProposeOrCancelDisabled"
                    x-on:click="if (!confirm('キャンセル処理を実施します。よろしいですか。\nこの処理は取り消しできません。')) { return; } $wire.cancelProgress();"
                >
                    キャンセル
                </x-button.red>
            </div>
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
                        {{ $averageLt['cost_received'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['owner_proposed'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['owner_approved'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['ordered'] }}
                    </td>
                    <td class="tw:text-[1.8rem] tw:font-bold tw:text-center tw:border tw:border-[#cccccc]">
                        {{ $averageLt['completion_received'] }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
