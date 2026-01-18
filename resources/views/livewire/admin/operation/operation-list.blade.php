<div>
    <table class="tw:w-full tw:text-[10pt]">
        <thead class="tw:sticky tw:top-0 tw:z-[150]">
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
                <tr class="tw:h-[63px] tw:border-b tw:border-b-pm_gray_005 tw:cursor-pointer" wire:click="selectThread({{ $thread->id }})">
                    <td class="tw:pl-[10px]">
                        {{ $thread->last_operation?->sent_at?->format('Y/m/d H:i:s') }}<br>
                        担当：{{ $thread->first_operation?->assignedUser?->full_name}}
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->operationTemplate->operation_category }}
                        <div class="tw:text-pm_gray_005">
                            {{ App\Models\OperationTemplate::OPERATION_TYPE[$thread->first_operation?->operationTemplate->operation_type] ?? '' }}
                        </div>
                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->investment_id }} {{ $thread->first_operation?->investment?->investment_name }}<br>
                        {{ $thread->first_operation?->investmentRoom?->investment_room_number }}
                    </td>
                    <td class="tw:pl-[5px]">
                        <div class="tw:flex tw:items-center tw:gap-x-1">
                            <div class="tw:w-[21px] tw:h-[21px]">
                                <x-owner.profile-icon :id="$thread->first_operation?->owner_id" />
                            </div>
                            {{ $thread->first_operation?->owner?->name }}
                        </div>


                    </td>
                    <td class="tw:pl-[5px]">
                        {{ $thread->first_operation?->operationKind?->value }}<br>
                        <div class="tw:text-pm_gray_005">
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
                        <div class="tw:text-pm_gray_005">
                            {{ (int) $thread->first_operation?->sent_at?->diffInDays(now()) }}日
                        </div>
                    </td>
                    <td class="tw:pl-[5px]">
                        <div class="tw:flex tw:items-center tw:gap-x-1">
                            <div class="tw:w-[21px] tw:h-[21px]">
                                <x-user.profile-icon :id="$thread->first_operation?->created_user_id" />
                            </div>
                            {{ $thread->first_operation?->createdUser?->full_name}}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div @class([
        'tw:w-[500px] tw:absolute tw:top-0 tw:right-0 tw:bg-white tw:border-l tw:transition-all tw:duration-300 tw:ease-in-out tw:z-[200]',
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
                    <?php if ($selectedThread?->status == App\Models\Thread::STATUS_PROPOSED): ?>
                        <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_green_001 tw:px-[9px]">
                            ✉  送信済
                        </div>
                    <?php elseif ($selectedThread?->status == App\Models\Thread::STATUS_REPROPOSED): ?>
                        <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_green_001 tw:px-[9px]">
                            ✉  再提案済
                        </div>
                    <?php elseif ($selectedThread?->status == App\Models\Thread::STATUS_OWNER_APPROVED): ?>
                        <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_green_001 tw:px-[9px]">
                            ✔  オーナー承諾済み
                        </div>
                    <?php elseif ($selectedThread?->status == App\Models\Thread::STATUS_OWNER_REJECTED): ?>
                        <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-red-100 tw:px-[9px]">
                            ✖  オーナー拒否
                        </div>
                    <?php elseif ($selectedThread?->status == App\Models\Thread::STATUS_CANCELED): ?>
                        <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_pink tw:px-[9px]">
                            ✖  中止
                        </div>
                    <?php endif; ?>

                    {{-- <div class="tw:w-full tw:h-[42px] tw:leading-[42px] tw:bg-pm_green_001 tw:px-[9px]">
                        ✔  オーナー承諾済み
                    </div> --}}
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
                <div class="tw:px-[21px] tw:pb-[21px]">
                    @if ($selectedThread?->threadMessages)
                        @foreach ($selectedThread?->threadMessages as $message)
                            <div class="tw:pt-[21px]">
                                <div class="tw:flex tw:justify-between tw:items-center tw:text-[10pt] tw:w-full">
                                    <div class="tw:flex tw:items-center tw:gap-x-1">
                                        <div class="tw:w-[20px] tw:h-[20px]">
                                            @if ($message->sender_type == App\Models\ThreadMessage::SENDER_TYPE_USER)
                                                <x-user.profile-icon :id="$message->sender_user_id" />
                                            @else
                                                <x-owner.profile-icon :id="$selectedThread?->owner_id" />
                                            @endif
                                        </div>
                                        <div class="tw:w-[180px] tw:truncate">
                                            @if ($message->sender_type == App\Models\ThreadMessage::SENDER_TYPE_USER)
                                                {{ $message->senderUser->full_name }}
                                            @else
                                                {{ $selectedThread?->owner?->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="tw:text-right">
                                        {{ $message->sent_at?->format('Y/m/d H:i:s') }}
                                    </div>
                                </div>
                                <div class="tw:mt-[5px]">
                                    <div class="tw:ml-[10px] tw:pl-[10px] tw:border-l tw:border-pm_gray_005">
                                        <div class="tw:border tw:border-pm_gray_005 tw:h-[42px] tw:leading-[42px] tw:pl-[9px]">
                                            {{ $message->title }}
                                        </div>
                                        <div class="tw:border tw:border-t-0 tw:border-pm_gray_005 tw:pl-[9px] tw:py-[21px]">
                                            <div>
                                                {!! nl2br(e($message->body)) !!}
                                            </div>
                                            <div>
                                                {!! nl2br(e($message->extended_message)) !!}
                                            </div>
                                        </div>
                                        @if ($message->operation)
                                            <div class="tw:bg-pm_blue_002 tw:h-[63px] tw:leading-[63px] tw:px-[9px] tw:text-[14pt] tw:text-pm_blue_001">
                                                <a href="">
                                                    <div class="tw:flex tw:justify-between">
                                                        <div>
                                                            提案詳細を見る
                                                        </div>
                                                        <div>
                                                            ＞
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="">
                <div class="tw:flex tw:justify-between tw:items-center tw:bg-pm_gray_004">
                    <div class="tw:text-[12pt] tw:p-[10px]">
                        詳細
                    </div>
                    <div class="tw:pr-[18px]">
                        <i class="fas fa-caret-up"></i>
                    </div>
                </div>
                <div class="tw:p-[21px] tw:text-[10pt]">
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            カテゴリ
                        </div>
                        <div>
                            {{ $selectedThread?->first_operation?->operationTemplate?->operation_category }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            アクティビティ
                        </div>
                        <div>
                            {{ $selectedThread?->first_operation?->operationKind?->value }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            アクティビティID
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            物件
                        </div>
                        <div>
                            {{ $selectedThread?->investment?->investment_name }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            部屋
                        </div>
                        <div>
                            {{ $selectedThread?->investment_room?->investment_room_number }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            オーナー
                        </div>
                        <div>
                            {{ $selectedThread?->owner->name }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            作成日時
                        </div>
                        <div>
                            {{ $selectedThread?->first_operation?->sent_at?->format('Y/m/d H:i:s') }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            最終更新日時
                        </div>
                        <div>
                            {{ $selectedThread?->last_operation?->sent_at?->format('Y/m/d H:i:s') }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[10px]">
                        <div class="tw:w-[150px]">
                            更新者
                        </div>
                        <div>
                            {{ $selectedThread?->last_operation?->assignedUser?->full_name }}
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>
