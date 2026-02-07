@props([
    'name' => 'files',
    'title' => '添付ファイル',
    'files' => [],
    'maxFileCount' => 0,
    'maxFileSize' => 0,
    'allowMimeTypes' => [],
    'selectEvent' => 'multi-file-upload2:selected',
    'removeEvent' => 'multi-file-upload2:removed',
    'instanceId' => null,
])

@php
    $existingFiles = collect($files ?? [])
        ->map(function ($file) {
            return [
                'id' => $file['id'] ?? null,
                'file_name' => $file['file_name'] ?? '',
                'url' => $file['url'] ?? null,
                'mime_type' => $file['mime_type'] ?? '',
            ];
        })
        ->values();

    //dd($existingFiles);
@endphp

<div
    x-data="multiFileUpload2Component(@js([
        'name' => $name,
        'instanceId' => $instanceId ?? $attributes->get('id') ?? $name,
        'maxFileCount' => $maxFileCount,
        'maxFileSize' => $maxFileSize,
        'allowMimeTypes' => $allowMimeTypes,
        'selectEvent' => $selectEvent,
        'removeEvent' => $removeEvent,
    ]))"
    class="tw:w-full tw:h-full"
    data-existing-count="{{ $existingFiles->count() }}"
    data-instance-id="{{ $instanceId ?? $attributes->get('id') ?? $name }}"
>
    <div class="">
        <button
            type="button"
            class="tw:text-[11pt] tw:text-blue-600 disabled:tw:text-gray-400 disabled:tw:no-underline"
            @click="openModal"
            @if($existingFiles->count() === 0) disabled @endif
        >
            ファイル数: {{ $existingFiles->count() }}件
        </button>
    </div>

    <div
        @click="triggerFileInput"
        @drop.prevent="handleDrop($event)"
        @dragover.prevent
        @dragenter.prevent
        @class([
            'tw:bg-[#cfe2f3] tw:border tw:border-gray-300 tw:mb-[5px] tw:text-[##666666] tw:cursor-pointer tw:flex tw:items-center tw:justify-center',
            $attributes->get('class'),
        ])
    >
        <div class="tw:text-center tw:text-[0.8rem]">
            <div>{{ $title }}</div>
            <i class="far fa-cloud-upload"></i>
        </div>
    </div>

    <input
        type="file"
        multiple
        x-ref="fileInput"
        @change="handleFileSelect"
        class="tw:hidden"
    >

    <template x-teleport="body">
        <div
            x-cloak
            x-show="isModalOpen"
            x-transition.opacity
            class="tw:fixed tw:inset-0 tw:z-[300] tw:flex tw:items-center tw:justify-center tw:bg-black/40 tw:px-[16px]"
            role="dialog"
            aria-modal="true"
            @click.self="closeModal"
        >
            <div
                x-show="isModalOpen"
                x-transition
                class="tw:w-full tw:max-w-[640px] tw:max-h-[80vh] tw:overflow-y-auto tw:rounded-[8px] tw:bg-white tw:shadow-lg"
            >
                <div class="tw:flex tw:items-center tw:justify-between tw:border-b tw:border-b-gray-200 tw:px-[16px] tw:py-[12px]">
                    <div class="tw:text-[1.2rem] tw:font-bold">ファイル一覧</div>
                    <button
                        type="button"
                        class="tw:text-[1.4rem] tw:text-gray-500"
                        @click="closeModal"
                        aria-label="閉じる"
                    >
                        ×
                    </button>
                </div>
                <div class="tw:px-[16px] tw:py-[12px]">
                    @if($existingFiles->isEmpty())
                        <div class="tw:text-[10pt] tw:text-gray-500">ファイルがありません</div>
                    @else
                        @foreach($existingFiles as $index => $file)
                            <div class="tw:flex tw:items-center tw:gap-[8px] tw:py-[6px] tw:border-b tw:border-gray-100">
                                <div class="tw:w-[16px]">
                                    <i class="far" :class="iconClass(@js($file['mime_type']))"></i>
                                </div>
                                <div class="tw:flex-1 tw:truncate">
                                    <button
                                        type="button"
                                        class="tw:text-blue-600 tw:underline tw:truncate"
                                        @click="openFile(@js($file))"
                                    >
                                        {{ $file['file_name'] }}
                                    </button>
                                </div>
                                <div class="tw:w-[22px] tw:text-right">
                                    <button type="button" class="tw:text-red-500" @click="requestRemove(@js($file))">
                                        <i class="fas fa-minus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="isPreviewOpen"
            x-transition.opacity
            class="tw:fixed tw:inset-0 tw:z-[320] tw:flex tw:items-center tw:justify-center tw:bg-black/50 tw:px-[16px]"
            role="dialog"
            aria-modal="true"
            @click.self="closePreview"
        >
            <div
                x-show="isPreviewOpen"
                x-transition
                class="tw:w-[80vw] tw:h-[80vh] tw:max-w-[1200px] tw:max-h-[80vh] tw:rounded-[8px] tw:bg-white tw:shadow-lg tw:flex tw:flex-col tw:overflow-hidden"
            >
                <div class="tw:flex tw:items-center tw:justify-between tw:border-b tw:border-b-gray-200 tw:px-[16px] tw:py-[12px]">
                    <div class="tw:text-[1rem] tw:font-bold tw:truncate" x-text="previewFile ? (previewFile.file_name || 'プレビュー') : 'プレビュー'"></div>
                    <button
                        type="button"
                        class="tw:text-[1.4rem] tw:text-gray-500"
                        @click="closePreview"
                        aria-label="閉じる"
                    >
                        ×
                    </button>
                </div>
                <div class="tw:flex-1 tw:bg-gray-50 tw:overflow-hidden">
                    <template x-if="previewFile && isImage(previewFile)">
                        <img
                            :src="previewFile.url"
                            :alt="previewFile.file_name || 'preview'"
                            class="tw:w-full tw:h-full tw:object-contain"
                        >
                    </template>
                    <template x-if="previewFile && isPdf(previewFile)">
                        <iframe
                            :src="previewFile.url"
                            class="tw:w-full tw:h-full"
                            title="PDF Preview"
                        ></iframe>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
@once
    @push('scripts')
        <script>
            function multiFileUpload2Component({ name, instanceId, maxFileCount, maxFileSize, allowMimeTypes, selectEvent, removeEvent }) {
                return {
                    isModalOpen: false,
                    isPreviewOpen: false,
                    previewFile: null,
                    maxFileSizeBytes: 0,
                    instanceId: instanceId || null,

                    init() {
                        this.maxFileSizeBytes = this.parseSize(maxFileSize);
                        this.instanceId = this.instanceId || this.$el.dataset.instanceId || name;
                    },

                    openModal() {
                        this.isModalOpen = true;
                    },

                    closeModal() {
                        this.isModalOpen = false;
                    },

                    openPreview(file) {
                        this.previewFile = file || null;
                        this.isPreviewOpen = !!this.previewFile;
                    },

                    closePreview() {
                        this.isPreviewOpen = false;
                        this.previewFile = null;
                    },

                    triggerFileInput() {
                        this.$refs.fileInput.click();
                    },

                    handleFileSelect(event) {
                        const selectedFiles = Array.from(event.target.files);
                        this.addFiles(selectedFiles);
                        event.target.value = '';
                    },

                    handleDrop(event) {
                        const droppedFiles = Array.from(event.dataTransfer.files);
                        this.addFiles(droppedFiles);
                    },

                    addFiles(files) {
                        const acceptedFiles = [];
                        let currentCount = this.getExistingCount();

                        files.forEach(file => {
                            if (!this.canAddFile(file, currentCount)) {
                                return;
                            }
                            acceptedFiles.push(file);
                            currentCount += 1;
                        });

                        if (acceptedFiles.length > 0) {
                            this.dispatchSelectEvent(acceptedFiles);
                        }
                    },

                    getExistingCount() {
                        const value = Number(this.$el.dataset.existingCount || 0);
                        return Number.isFinite(value) ? value : 0;
                    },

                    canAddFile(file, currentCount) {
                        if (maxFileCount > 0 && currentCount >= maxFileCount) {
                            alert('これ以上アップロードできません');
                            return false;
                        }
                        if (this.maxFileSizeBytes > 0 && file.size > this.maxFileSizeBytes) {
                            alert(file.name + ' はサイズオーバーです');
                            return false;
                        }
                        if (allowMimeTypes.length > 0 && !allowMimeTypes.includes(file.type)) {
                            alert(file.name + ' はアップロードできません');
                            return false;
                        }
                        return true;
                    },

                    dispatchSelectEvent(files) {
                        window.dispatchEvent(new CustomEvent(selectEvent, {
                            detail: {
                                name,
                                instanceId: this.instanceId,
                                files,
                            },
                        }));
                    },

                    requestRemove(file) {
                        window.dispatchEvent(new CustomEvent(removeEvent, {
                            detail: {
                                name,
                                instanceId: this.instanceId,
                                file,
                            },
                        }));
                    },

                    openFile(file) {
                        if (!file || !file.url) {
                            return;
                        }
                        if (this.isPreviewable(file)) {
                            this.openPreview(file);
                            return;
                        }

                        const link = document.createElement('a');
                        link.href = file.url;
                        link.download = file.file_name || '';
                        link.rel = 'noopener';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    },

                    isPreviewable(file) {
                        return this.isPdf(file) || this.isImage(file);
                    },

                    isPdf(file) {
                        const mime = (file.mime_type || '').toLowerCase();
                        if (mime === 'application/pdf') {
                            return true;
                        }
                        const name = (file.file_name || '').toLowerCase();
                        return name.endsWith('.pdf');
                    },

                    isImage(file) {
                        const mime = (file.mime_type || '').toLowerCase();
                        if (mime.includes('image')) {
                            return true;
                        }
                        const name = (file.file_name || '').toLowerCase();
                        return name.endsWith('.png') || name.endsWith('.jpg') || name.endsWith('.jpeg') || name.endsWith('.gif') || name.endsWith('.webp') || name.endsWith('.bmp');
                    },

                    iconClass(mimeType) {
                        if (mimeType === 'application/pdf') {
                            return 'fa-file-pdf';
                        }
                        if (
                            mimeType === 'application/vnd.ms-excel' ||
                            mimeType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                            mimeType.includes('excel')
                        ) {
                            return 'fa-file-excel';
                        }
                        if (mimeType.includes('image')) {
                            return 'fa-file-image';
                        }
                        return 'fa-file';
                    },

                    parseSize(value) {
                        if (typeof value === 'number') {
                            return value;
                        }
                        if (value === null || value === undefined) {
                            return 0;
                        }

                        const normalized = String(value).trim().toUpperCase();
                        if (!normalized) return 0;

                        const match = normalized.match(/^(\d+(?:\.\d+)?)\s*(KB|MB)?$/);
                        if (!match) {
                            const numeric = Number(normalized);
                            return Number.isFinite(numeric) ? numeric : 0;
                        }

                        const size = Number(match[1]);
                        if (!Number.isFinite(size)) return 0;

                        if (!match[2]) {
                            return size;
                        }
                        if (match[2] === 'KB') {
                            return size * 1024;
                        }
                        return size * 1024 * 1024;
                    },
                };
            }
        </script>
    @endpush
@endonce
