<div
    class="tw:w-[832px]"
    x-data="geProgressStep1"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        STEP１（退去立会依頼）
    </div>
    <div class="tw:w-full tw:px-[26px] tw:pt-[26px]">
        <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">実行担当入力（AQUTICS）</div>
        <table class="tw:w-full tw:table-fixed">
            <tr class="tw:h-[42px]">
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">敷金預託等</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="securityDepositAmount" class="tw:text-right tw:text-[1.2rem]" :border="false" wire:model.live="securityDepositAmount" />
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">日割り家賃</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="proratedRentAmount" class="tw:text-right tw:text-[1.2rem]" :border="false" wire:model.live="proratedRentAmount" />
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">違約金（償却）</td>
                <td class="tw:border tw:border-[#cccccc]">
                    <x-form.input-number name="penaltyForfeitureAmount" class="tw:text-right tw:text-[1.2rem]" :border="false" wire:model.live="penaltyForfeitureAmount" />
                </td>
            </tr>
        </table>
        @error('securityDepositAmount')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror
        @error('proratedRentAmount')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror
        @error('penaltyForfeitureAmount')
            <x-form.error-message>{{ $message }}</x-form.error-message>
        @enderror

        <table class="tw:w-full tw:table-fixed tw:mt-[42px]">
            <tr class="tw:h-[42px]">
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">退去受付</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress->taikyo_uketuke_date?->format('Y/m/d') }}
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">革命登録</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress->kakumei_koujo_touroku_date?->format('Y/m/d') }}
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">退去報告</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress->geProgress?->move_out_report_date?->format('Y/m/d') }}
                </td>
            </tr>
            <tr class="tw:h-[42px]">
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">解約日</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress?->investmentEmptyRoom?->cancellation_date?->format('Y/m/d') }}
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">退去日</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress->taikyo_date?->format('Y/m/d') }}
                </td>
                <td class="tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">見積り受信日</td>
                <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">
                    {{ $progress->genpuku_mitsumori_recieved_date?->format('Y/m/d') }}
                </td>
            </tr>
        </table>
        <div class="tw:h-[63px] tw:mb-[21px] tw:flex tw:gap-x-[26px] tw:items-end">
            <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">立会パッケージ</x-button.black>
            <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">退去時精算書</x-button.black>
        </div>
        <div class="tw:mb-[21px]">
            <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">
                添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
            </div>
            <div class="tw:w-full">
                <x-form.multi_file_upload2
                    name="step1_files"
                    instanceId="ge-progress-step1-{{ $progress->id }}"
                    class="tw:h-[42px]"
                    maxFileCount="20"
                    maxFileSize="25MB"
                    :allowMimeTypes="[
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',
                        'image/bmp',
                        'image/tiff',
                        'image/heic',
                        'image/heif',
                        'application/pdf',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ]"
                    :files="$step1Files"
                />
            </div>
        </div>
        <div class="tw:mb-[21px]">
            立会依頼メッセージ<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント" wire:model.live="inspectionRequestMessage"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-form.checkbox
                    name="step1Confirmed"
                    :checked="$step1Confirmed"
                    label_class="tw:!text-[1.1rem]"
                    wire:model.live="step1Confirmed"
                >
                    立会パッケージ、退去時清算書格納の確認
                </x-form.checkbox>
            </div>
            <div>
                <x-button.blue
                    class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]"
                    :disabled="!$step1Confirmed"
                    type="button"
                    x-on:click="if (!confirm('立会依頼送信します。よろしいですか。')) { return; } $wire.updateMoveOutReportDate();"
                >
                    立会依頼送信
                </x-button.blue>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('geProgressStep1', () => ({
                instanceId: @js('ge-progress-step1-' . $progress->id),
                handleSelect(event) {
                    if (event?.detail?.name !== 'operation_files') {
                        return;
                    }
                    if (event?.detail?.instanceId !== this.instanceId) {
                        return;
                    }
                    const files = event.detail?.files || [];
                    if (files.length === 0) {
                        return;
                    }
                    this.$wire.uploadMultiple('step1Uploads', files, () => {
                        this.$wire.call('saveStep1Uploads');
                    });
                },
                handleRemove(event) {
                    if (event?.detail?.name !== 'operation_files') {
                        return;
                    }
                    if (event?.detail?.instanceId !== this.instanceId) {
                        return;
                    }
                    const fileId = event?.detail?.file?.id || null;
                    this.$wire.call('removeStep1File', fileId);
                },
            }));
        });
    </script>
@endpush
