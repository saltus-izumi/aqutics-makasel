<div>
    <table class="tw:w-full tw:text-[10pt]">
        <thead>
            <tr class="tw:h-[42px] tw:bg-pm_gray_004">
                <th class="tw:pl-[10px] tw:font-normal tw:text-left tw:min-w-[190px]">更新日/所有係</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[114px]">カテゴリ</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[190px]">物件ID / 物件名 / 号室</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[114px]">オーナー個人名</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[228px]">アクティビティ</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[152px]">ステータス</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[76px]">既読/未読</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[114px]">作成日</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[76px]">作成者</th>
                <th class="tw:pl-[5px] tw:font-normal tw:text-left tw:min-w-[76px]"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($threads as $thread)
                <tr class="tw:h-[63px] tw:border-b tw:border-b-[#bdbdbd] tw:cursor-pointer" wire:click="selectThread({{ $thread->id }})">
                    <td class="tw:pl-[10px]">
                        {{ $thread->last_operation?->sent_at?->format('Y/m/d H:i:s') }}<br>
                        担当：{{ $thread->first_operation?->assignedUser?->full_name}}
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->operationTemplate->operation_category }}
                        <div class="tw:text-[#bdbdbd]">
                            {{ App\Models\OperationTemplate::OPERATION_TYPE[$thread->first_operation?->operationTemplate->operation_type] ?? '' }}
                        </div>
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->investment_id }} {{ $thread->first_operation?->investment?->investment_name }}<br>
                        {{ $thread->first_operation?->investmentRoom?->investment_room_number }}
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->owner?->name }}
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->operationKind?->value }}<br>
                        <div class="tw:text-[#bdbdbd]">
                            {{ $thread->first_operation?->threadMessage?->title }}
                        </div>
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ App\Models\Operation::STATUS[$thread->last_operation?->status] ?? '' }}<br>
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->last_operation?->read_at ? '既読' : '未読' }}<br>
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->sent_at?->format('Y/m/d') }}
                        <div class="tw:text-[#bdbdbd]">
                            {{ (int) $thread->first_operation?->sent_at?->diffInDays(now()) }}日
                        </div>
                    </td>
                    <td class="tw:pl-[5px]">
                        <x-user.profile-icon :id="$thread->first_operation?->created_user_id" />
                        {{ $thread->first_operation?->createdUser?->full_name}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div @class([
        'tw:w-[500px] tw:absolute tw:top-0 tw:right-0 tw:bg-white tw:border-l tw:transition-all tw:duration-300 tw:ease-in-out',
        'tw:translate-x-0 tw:opacity-100' => $selectedThreadId,
        'tw:translate-x-full tw:opacity-0 tw:pointer-events-none' => ! $selectedThreadId,
    ])>
        <div class="tw:h-screen tw:overflow-y-auto">
            <div>
                <div class="tw:flex tw:justify-between tw:items-center tw:bg-pm_gray_004">
                    <div class="tw:text-[12pt] tw:p-[10px]">
                        アクティビティ詳細
                    </div>
                    <div class="tw:pr-[18px]">
                        <i class="fas fa-times tw:cursor-pointer" wire:click="closeDetail"></i>
                    </div>
                </div>
                <div class="tw:py-[21px] tw:px-[22px]">
                    <div class="tw:pl-[18px]">
                        現在ステータス
                    </div>
                    <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_green_001 tw:px-[9px]">
                        ✔  オーナー承諾済み
                    </div>
                    <div class="tw:border tw:border-pm_gray_003 tw:mt-[21px] tw:h-[42px] tw:leading-[42px] tw:text-center">
                        チャットを開く
                    </div>
                </div>
            </div>
            <div class="">
                <div class="tw:flex tw:justify-between tw:items-center tw:bg-pm_gray_004">
                    <div class="tw:text-[12pt] tw:p-[10px]">
                        アクティビティ履歴
                    </div>
                    <div class="tw:pr-[18px]">
                        <i class="fas fa-caret-up"></i>
                    </div>
                </div>
                <div class="tw:px-[21px]">
                    <div class="tw:pt-[21px]">
                        <div class="tw:flex tw:justify-between tw:items-center tw:text-[10pt] tw:w-full">
                            <div class="tw:flex tw:items-center tw:gap-x-[4px]">
                                <div class="tw:w-[20px] tw:h-[20px]">
                                </div>
                                <div class="tw:w-[180px] tw:truncate">
                                </div>
                            </div>
                            <div class="tw:text-right">
                                2022/12/24 15:30:00
                            </div>
                        </div>
                        <div class="tw:mt-[5px]">
                            <div class="tw:ml-[10px] tw:pl-[10px] tw:border-l tw:border-pm_gray_2">
                                <div class="tw:border tw:border-pm_gray_2 tw:h-[42px] tw:leading-[42px] tw:pl-[9px]">
                                    オーナー承諾済
                                </div>
                                <div class="tw:border tw:border-t-0 tw:border-pm_gray_2 tw:pl-[9px]">
                                    物件名：<br>
                                    部屋：<br>
                                    <br>
                                    いつも大変お世話になっております。<br>
                                    新規募集提案のついてご提案致しますので、ご確認いただけますでしょうか。ご承諾いただける場合は、【承諾する】ボタンを押下いただけますでしょうか。なお、ご承諾いただけない場合は、ご理由やご指示をいただけますと幸いです。<br>
                                    お忙しいところ、恐縮ですがご確認何卒よろしくお願い申し上げます。<br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
