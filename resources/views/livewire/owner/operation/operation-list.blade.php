<div>
    <table class="tw:w-full">
        <thead class="tw:sticky tw:top-0 tw:z-[150]">
            <tr class="tw:bg-pm_gray_004">
                <th class="tw:min-w-[130px] tw:font-normal"></th>
                <th class="tw:min-w-[442px] tw:font-normal tw:text-left">オペレーション内容</th>
                <th class="tw:min-w-[130px] tw:max-w-[130px] tw:font-normal">ステータス</th>
                <th class="tw:min-w-[416px] tw:font-normal">コメント</th>
                <th class="tw:min-w-[182px] tw:font-normal">添付</th>
            </tr>
        </thead>
        <tbody>
            @foreach($threads as $thread)
                <tr class="tw:h-[147px] tw:border-b" wire:click="selectThread({{ $thread->id }})">
                    <td>
                        <div class="tw:w-full tw:h-full tw:flex tw:items-center tw:justify-center">
                            <div class="tw:w-[78px] tw:h-[105px] tw:bg-[#cccccc] tw:text-center tw:leading-[105px] tw:text-[1.3rem] tw:font-bold tw:text-[#666666]">
                                {{ App\Models\OperationTemplate::SHORT_OPERATION_GROUPS[$thread->first_operation?->operationTemplate->operation_group_id] ?? '' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="tw:text-pm_gray_005 tw:text-[1.2rem] tw:h-[21px] tw:leading-[21px]">
                            {{ $thread->last_operation?->sent_at?->format('Y年m月d日 | H:i') }}
                        </div>
                        <div class="tw:h-[42px] tw:leading-[42px]">
                            <span class="tw:text-[1.4rem] tw:font-bold tw:text-pm_blue_001">{{ $thread->first_operation?->operationKind?->value }}</span>
                            <span class="tw:text-pm_gray_005">（{{ $thread->first_operation?->operationTemplate->operation_category }}）</span>
                        </div>
                        <div class="tw:h-[42px] tw:leading-[42px]">
                            <span class="tw:text-[1.4rem] tw:font-bold">{{ $thread->first_operation?->investment?->investment_name }}｜</span>
                            <span class="tw:text-[1.5rem] tw:font-bold">{{ $thread->first_operation?->investmentRoom?->investment_room_number }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="tw:w-full tw:h-full tw:flex tw:items-center tw:justify-center">
                            @if ($thread->status == App\Models\Thread::STATUS_PROPOSED)
                                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-pm_blue_001">
                                    {{ App\Models\Thread::OWNER_STATUS[$thread->status] ?? '' }}
                                </div>
                            @elseif ($thread->status == App\Models\Thread::STATUS_REPROPOSED)
                                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-pm_blue_001">
                                    {{ App\Models\Thread::OWNER_STATUS[$thread->status] ?? '' }}
                                </div>
                            @elseif ($thread->status == App\Models\Thread::STATUS_OWNER_APPROVED)
                                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-black">
                                    {{ App\Models\Thread::OWNER_STATUS[$thread->status] ?? '' }}
                                </div>
                            @elseif ($thread->status == App\Models\Thread::STATUS_OWNER_REJECTED)
                                <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-[#ff0000]">
                                    {{ App\Models\Thread::OWNER_STATUS[$thread->status] ?? '' }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="tw:flex tw:items-center tw:gap-x-2">
                            <div class="tw:w-[42px] tw:h-[42px]">
                                <x-admin.profile-icon :id="$thread->first_operation?->created_user_id" />
                            </div>
                            <span class="tw:text-[1.3rem]">{{ $thread->first_operation?->createdUser?->full_name}}</div>
                        </div>
                        <div class="tw:flex-1 tw:text-wrap tw:overflow-hidden tw:line-clamp-3 tw:pr-[10px]">
                            {!! nl2br(e($thread->last_message?->body)) !!}
                        </div>
                    </td>
                    <td class="tw:h-[147px] tw:py-[21px] tw:border-b tw:border-pm_gray_1">
                        <div class="tw:flex tw:flex-wrap tw:gap-x-[10px]">
                            @foreach ($thread->first_operation?->retailEstimateFiles as $retailEstimateFile)
                                <div><?= h($retailEstimateFile->teProgressFile?->file_name) ?></div>
                            @endforeach
                            @foreach ($thread->first_operation?->completionPhotoFiles as $completionPhotoFile)
                                <div><?= h($completionPhotoFile->teProgressFile?->file_name) ?></div>
                            @endforeach
                            @foreach ($thread->first_operation?->otherFiles as $otherFile)
                                <div>{{ $otherFile?->file_name }}</div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div @class([
        'tw:w-[624px] tw:absolute tw:top-0 tw:right-0 tw:bg-white tw:border-l tw:transition-all tw:duration-300 tw:ease-in-out tw:z-[200]',
        'tw:translate-x-0 tw:opacity-100' => $selectedThreadId,
        'tw:translate-x-full tw:opacity-0 tw:pointer-events-none' => ! $selectedThreadId,
    ])>
        <div class="tw:h-screen tw:overflow-y-auto">
            <div class="tw:px-[26px] tw:py-[21px]">
                @if ($selectedThread?->threadMessages)
                    @foreach ($selectedThread?->threadMessages as $message)
                        @if ($message->operation)
                            <div class="tw:mb-[21px] tw:flex tw:gap-x-[26px]">
                                <div class="tw:w-[78px] tw:h-[84px] tw:bg-[#cccccc] tw:text-center tw:leading-[84px] tw:text-[1.3rem] tw:font-bold tw:text-white">
                                    {{ App\Models\OperationTemplate::SHORT_OPERATION_GROUPS[$message?->operation?->operationTemplate->operation_group_id] ?? '' }}
                                </div>
                                <div>
                                    <div class="tw:h-[42px] tw:leading-[42px]">
                                        <span class="tw:text-[1.4rem] tw:font-bold tw:text-pm_blue_001">{{ $message?->operation?->operationKind?->value }}</span>
                                        <span class="tw:text-pm_gray_005">（{{ $message?->operation?->operationTemplate->operation_category }}）</span>
                                    </div>
                                    <div class="tw:h-[42px] tw:leading-[42px]">
                                        <span class="tw:text-[1.4rem] tw:font-bold">{{ $thread->first_operation?->investment?->investment_name }}｜</span>
                                        <span class="tw:text-[1.5rem] tw:font-bold">{{ $thread->first_operation?->investmentRoom?->investment_room_number }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tw:mb-[21px] tw:flex tw:gap-x-[26px]">
                                @if ($message?->operation?->status == App\Models\Operation::STATUS_IN_PROGRESS)
                                    <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-pm_blue_001">
                                        {{ App\Models\Operation::OWNER_STATUS[$message?->operation?->status] ?? '' }}
                                    </div>
                                @elseif ($message?->operation?->status == App\Models\Operation::STATUS_CONFIRMED)
                                    <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-pm_blue_001">
                                        {{ App\Models\Operation::OWNER_STATUS[$message?->operation?->status] ?? '' }}
                                    </div>
                                @elseif ($message?->operation?->status == App\Models\Operation::STATUS_APPROVED)
                                    <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-black">
                                        {{ App\Models\Operation::OWNER_STATUS[$message?->operation?->status] ?? '' }}
                                    </div>
                                @elseif ($message?->operation?->status == App\Models\Operation::STATUS_REJECTED)
                                    <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:leading-[42px] tw:text-[1.4rem] tw:text-white tw:font-bold tw:bg-[#ff0000]">
                                        {{ App\Models\Operation::OWNER_STATUS[$message?->operation?->status] ?? '' }}
                                    </div>
                                @endif
                                <div>
                                    <div class="tw:h-[21px] tw:leading-[21px] tw:text-[1.2rem] tw:text-pm_gray_005">
                                        {{ $message?->operation?->sent_at?->format('Y年m月d日 | H:i') }}
                                    </div>
                                    <div class="tw:h-[30px] tw:leading-[30px] tw:text-[1.3rem]">
                                        担当：{{ $message?->operation?->createdUser?->full_name}}
                                    </div>
                                </div>
                            </div>
                            <div class="tw:text-[1.3rem] tw:mb-[21px]">
                                {!! nl2br(e($message->body)) !!}
                            </div>
                            <div class="tw:text-[1.3rem] tw:mb-[21px]">
                                {!! nl2br(e($message->extended_message)) !!}
                            </div>
                            <div class="tw:pt-[21px] tw:flex tw:gap-x-[21px]">
                                @if ($message?->operation?->retailEstimateFiles?->count() > 0)
                                    <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-retail-estimate-files">お見積書</div>
                                @endif
                                @if ($message?->operation?->completionPhotoFiles?->count() > 0)
                                    <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-completion-photo-files" wire:click="showCompletionPhotoFiles({{ $message->operation?->id }})">写真</div>
                                @endif
                                @if ($message?->operation?->otherFiles?->count() > 0)
                                    <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-other-files" wire:click="showOtherFiles({{ $message->operation?->id }})">その他のファイル</div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <x-modal :title="$modalTitle">
        @forelse ($modalFiles as $fileName)
            <div>{{ $fileName }}</div>
        @empty
            <div>ファイルがありません。</div>
        @endforelse
    </x-modal>

    <div id="retail-estimate-files-modal" class="m-modal" tabindex="-1">
        <div class="m-modal-overlay" tabindex="-1" data-micromodal-close>
            <div class="m-modal-container">
                <div class="tw-w-[546px] tw-py-[21px] tw-px-[26px] tw-text-[15pt]">
                    <span>お見積書</span>
                </div>
            </div>
        </div>
    </div>
    <div id="completion-photo-files-modal" class="m-modal" tabindex="-1">
        <div class="m-modal-overlay" tabindex="-1" data-micromodal-close>
            <div class="m-modal-container">
                <div class="tw-w-[546px] tw-py-[21px] tw-px-[26px] tw-text-[15pt]">
                    <span>写真</span>
                </div>
            </div>
        </div>
    </div>
    <div id="other-files-modal" class="m-modal" tabindex="-1">
        <div class="m-modal-overlay" tabindex="-1" data-micromodal-close>
            <div class="m-modal-container">
                <div class="tw-w-[546px] tw-py-[21px] tw-px-[26px] tw-text-[15pt]">
                    <span>その他のファイル</span>
                </div>
            </div>
        </div>
    </div>

</div>
