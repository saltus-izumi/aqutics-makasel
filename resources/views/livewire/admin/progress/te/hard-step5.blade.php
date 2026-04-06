<div
    class="tw:w-[832px]"
    x-data="teProgressHardStep5"
    @multi-file-upload2:selected.window="handleSelect($event)"
    @multi-file-upload2:removed.window="handleRemove($event)"
>
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3] tw:text-[1.1rem]">
        ハード｜STEP５（完了報告）
    </div>
    <div class="tw:w-full tw:px-[26px]">
        <div class="tw:mt-[21px]">
            <div class="tw:w-full">
                <x-form.multi_file_upload2
                    name="completion_photo"
                    title="その他完工写真"
                    instanceId="te-progress-completion-photo-{{ $teProgress->id }}"
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
                    :files="$completionPhotoFiles"
                />
            </div>
            <div class="tw:h-[42px] tw:flex tw:justify-center tw:items-start">
                @if ($teProgress?->completion_report_operation_id)
                    <a href="{{ route('admin.operation.edit', [
                        'operationId' => $teProgress?->completion_report_operation_id,
                    ]) }}">
                        <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション編集</x-button.blue>
                    </a>
                @else
                    <a href="{{ route('admin.operation.create.te', [
                        'teProgressId' => $teProgress?->id,
                        'progressStep' => 'completion_report',
                    ]) }}">
                        <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">オペレーション作成</x-button.blue>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('teProgressHardStep5', () => ({
                instanceMap: [
                    {
                        instanceId: @js('te-progress-completion-photo-' . $teProgress->id),
                        uploadProperty: 'completionPhotoUploads',
                        saveMethod: 'saveCompletionPhotoUploads',
                        removeMethod: 'removeCompletionPhotoFile',
                    },
                ],
                handleSelect(event) {
                    const target = this.instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                    if (!target) {
                        return;
                    }
                    const files = event.detail?.files || [];
                    if (files.length === 0) {
                        return;
                    }
                    this.$wire.uploadMultiple(target.uploadProperty, files, () => {
                        this.$wire.call(target.saveMethod);
                    });
                },
                handleRemove(event) {
                    const target = this.instanceMap.find((item) => item.instanceId === event?.detail?.instanceId);
                    if (!target) {
                        return;
                    }
                    const fileId = event?.detail?.file?.id || null;
                    this.$wire.call(target.removeMethod, fileId);
                },
            }));
        });
    </script>
@endpush
