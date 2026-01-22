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
                    <td @class([
                        'tw:border-l-[4px] tw:border-l-pm_blue_001' => $thread->id == $selectedThreadId
                    ])>
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
                            @if ($thread->last_message->sender_type == App\Models\ThreadMessage::SENDER_TYPE_USER)
                                <div class="tw:w-[21px] tw:h-[21px]">
                                    <x-admin.profile-icon :id="$thread->last_message?->sender_user_id" />
                                </div>
                                <span class="tw:text-[1.3rem]">{{ $thread->last_message?->senderUser?->full_name}}</div>
                            @else
                                <div class="tw:w-[21px] tw:h-[21px]">
                                    <x-owner.profile-icon :id="$thread->owner_id" />
                                </div>
                                <span class="tw:text-[1.3rem]">{{ $thread->owner?->name}}</div>
                            @endif
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
                        <div class="tw:py-[21px] tw:border-b">
                            @if ($message->operation)
                                @continue($message->operation->status == App\Models\Operation::STATUS_DRAFT)
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
                                <div class="tw:py-[21px] tw:flex tw:gap-x-[21px] tw:border-b tw:border-b-[#cccccc]">
                                    @if ($message?->operation?->retailEstimateFiles?->count() > 0)
                                        <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-retail-estimate-files" wire:click="showRetailEstimateFiles({{ $message->operation?->id }})">お見積書</div>
                                    @endif
                                    @if ($message?->operation?->completionPhotoFiles?->count() > 0)
                                        <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-completion-photo-files" wire:click="showCompletionPhotoFiles({{ $message->operation?->id }})">写真</div>
                                    @endif
                                    @if ($message?->operation?->otherFiles?->count() > 0)
                                        <div class="tw:w-[130px] tw:h-[42px] tw:leading-[42px] tw:text-center tw:text-[10pt] tw:bg-[#999999] tw:cursor-pointer js-btn-other-files" wire:click="showOtherFiles({{ $message->operation?->id }})">その他のファイル</div>
                                    @endif
                                </div>
                                @if ($message->operation?->status == App\Models\Operation::STATUS_IN_PROGRESS || $message->operation?->status == App\Models\Operation::STATUS_CONFIRMED)
                                    <div class="tw:py-[21px] tw:text-[1.2rem] tw:text-center">
                                        回答をお選びください
                                        <div class="tw:pt-[21px] tw:flex tw:gap-x-[26px] tw:justify-center">
                                            <x-button.blue class="tw:text-[1.4rem] tw:font-bold tw:w-[150px]" wire:click="showAcceptModal({{ $message->operation?->id }})">
                                                承諾
                                            </x-button.blue>
                                            <x-button.red class="tw:text-[1.4rem] tw:font-bold tw:w-[150px]" wire:click="showRejectModal({{ $message->operation?->id }})">
                                                却下
                                            </x-button.red>
                                            <x-button.black class="tw:text-[1.3rem] tw:font-bold tw:w-[150px]" wire:click="showMessageModal()">
                                                メッセージ
                                            </x-button.black>
                                        </div>
                                    </div>
                                @elseif ($message->operation?->status == App\Models\Operation::STATUS_APPROVED)
                                    <div class="tw:py-[21px] tw:text-[1.2rem] tw:text-center">
                                        ご回答済
                                        <div class="tw:pt-[21px] tw:flex tw:gap-x-[26px] tw:justify-center">
                                            <div class="tw:h-[42px] tw:leading-[42px] tw:bg-pm_blue_001 tw:text-white tw:text-[1.4rem] tw:font-bold tw:w-[150px]">
                                                承諾
                                            </div>
                                            <x-button.black class="tw:text-[1.3rem] tw:font-bold tw:w-[150px]" wire:click="showMessageModal()">
                                                メッセージ
                                            </x-button.black>
                                        </div>
                                    </div>
                                @elseif ($message->operation?->status == App\Models\Operation::STATUS_REJECTED)
                                    <div class="tw:py-[21px] tw:text-[1.2rem] tw:text-center">
                                        ご回答済
                                        <div class="tw:pt-[21px] tw:flex tw:gap-x-[26px] tw:justify-center">
                                            <div class="tw:h-[42px] tw:leading-[42px] tw:bg-[#ff0000] tw:text-white tw:text-[1.4rem] tw:font-bold tw:w-[150px]">
                                                却下
                                            </div>
                                            <x-button.black class="tw:text-[1.3rem] tw:font-bold tw:w-[150px]" wire:click="showMessageModal()">
                                                メッセージ
                                            </x-button.black>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="tw:mb-[21px] tw:flex tw:items-center tw:gap-x-[26px]">
                                    <div class="tw:h-[21px] tw:leading-[21px] tw:text-[1.2rem] tw:text-pm_gray_005">
                                        {{ $message?->sent_at?->format('Y年m月d日 | H:i') }}
                                    </div>
                                    <div class="tw:h-[30px] tw:leading-[30px] tw:text-[1.3rem]">
                                        {{ $selectedThread?->owner->name}}
                                    </div>
                                </div>
                                <div class="tw:text-[1.3rem]">
                                    {!! nl2br(e($message->body)) !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <x-modal :title="$modalTitle" event="files-modal">
        @forelse ($modalFiles as $file)
            <button
                type="button"
                class="tw:w-full tw:text-left tw:text-pm_blue_001 tw:underline tw:underline-offset-2"
                wire:click="showFilePreview({{ $file['id'] }})"
            >
                {{ $file['name'] }}
            </button>
        @empty
            <div>ファイルがありません。</div>
        @endforelse
    </x-modal>

    <x-modal :title="$previewTitle" event="preview-modal">
        @if ($previewType === 'pdf')
            <iframe class="tw:w-full tw:h-[70vh]" src="{{ $previewUrl }}"></iframe>
        @elseif ($previewType === 'image')
            <img class="tw:w-full tw:h-auto" src="{{ $previewUrl }}" alt="{{ $previewTitle }}">
        @else
            <div>プレビューできません。</div>
            <a class="tw:text-pm_blue_001 tw:underline tw:underline-offset-2" href="{{ $previewUrl }}" target="_blank" rel="noopener">別タブで開く</a>
        @endif
    </x-modal>

    <x-modal title="ご回答" event="accept-modal">
        <div>
            <span class="tw:text-[1.5rem]">コメント</span>（なしの場合でも空白で送信ボタンを押して下さい）
        </div>
        <x-form.textarea name="body" rows="13" wire:model="body"></x-form.textarea>
        @error('body')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror
        <div class="tw:pt-[21px]">
            <x-button.blue class="tw:w-[104px]" wire:click="answer('{{ App\Models\Operation::STATUS_APPROVED }}', {{ $selectedOperationId }})">送信</x-button.blue>
        </div>
    </x-modal>

    <x-modal title="ご回答" event="reject-modal">
        <div>
            <span class="tw:text-[1.5rem]">理由｜ご指示</span><span class="tw:text-[1.5rem] tw:text-[#ff0000]">＊必須</span>
        </div>
        <x-form.textarea name="body" rows="13" wire:model="body"></x-form.textarea>
        @error('body')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror
        <div class="tw:pt-[21px]">
            <x-button.blue class="tw:w-[104px]" wire:click="answer('{{ App\Models\Operation::STATUS_REJECTED }}', {{ $selectedOperationId }})">送信</x-button.blue>
        </div>
    </x-modal>

    <x-modal title="メッセージ" event="message-modal">
        <div>
            <span class="tw:text-[1.5rem]">メッセージ</span><span class="tw:text-[1.5rem] tw:text-[#ff0000]">＊必須</span>
        </div>
        <x-form.textarea name="body" rows="13" wire:model="body"></x-form.textarea>
        @error('body')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror
        <div class="tw:pt-[21px]">
            <x-button.blue class="tw:w-[104px]" wire:click="message()">送信</x-button.blue>
        </div>
    </x-modal>
</div>
