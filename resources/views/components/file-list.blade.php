@props([
    'files' => [],
    'removeIds' => [],
    'title' => null,
    'emptyText' => 'ファイルがありません',
    'removeEvent' => 'multi-file-upload2:removed',
    'instanceId' => null,
])

@php
    $normalizedFiles = collect($files ?? [])
        ->map(function ($value, $key) {
            if (is_array($value)) {
                return [
                    'url' => $value['url'] ?? null,
                    'file_name' => $value['file_name'] ?? ($value['name'] ?? ''),
                ];
            }

            if (is_string($key)) {
                return [
                    'url' => $key,
                    'file_name' => is_scalar($value) ? (string) $value : '',
                ];
            }

            if (is_string($value)) {
                return [
                    'url' => $value,
                    'file_name' => basename($value),
                ];
            }

            return [
                'url' => null,
                'file_name' => '',
            ];
        })
        ->filter(function ($file) {
            return !empty($file['url']);
        })
        ->values();

    $removeMap = collect($removeIds ?? [])
        ->filter(function ($id, $url) {
            return !empty($url) && !empty($id);
        })
        ->all();

    $normalizedFiles = $normalizedFiles
        ->map(function ($file) use ($removeMap) {
            $file['remove_id'] = $removeMap[$file['url']] ?? null;
            return $file;
        })
        ->values();
@endphp

<div {{ $attributes->merge(['class' => 'tw:w-full']) }}>
    <div
        x-data="fileListComponent(@js([
            'instanceId' => $instanceId ?? $attributes->get('data-instance-id') ?? null,
            'removeEvent' => $removeEvent,
            'removeMap' => $removeMap,
        ]))"
        class="tw:w-full"
    >
        @if($title)
            <div class="tw:text-[1.2rem] tw:font-bold">{{ $title }}</div>
        @endif

        @if($normalizedFiles->isEmpty())
            <div class="tw:text-[10pt] tw:text-gray-500">{{ $emptyText }}</div>
        @else
            @foreach($normalizedFiles as $file)
                <div class="tw:flex tw:items-center tw:gap-[8px] tw:py-[6px] tw:border-b tw:border-gray-100">
                    <div class="tw:w-[16px]">
                        <i class="far" :class="iconClass(@js($file))"></i>
                    </div>
                    <div class="tw:flex-1 tw:truncate">
                        <button
                            type="button"
                            class="tw:text-blue-600 tw:underline tw:truncate"
                            @click="openFile(@js($file))"
                        >
                            {{ $file['file_name'] ?: $file['url'] }}
                        </button>
                    </div>
                    @if(!empty($file['remove_id']))
                        <div class="tw:w-[22px] tw:text-right">
                            <button type="button" class="tw:text-red-500" @click="requestRemove(@js($file))">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

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
</div>

@once
    @push('scripts')
        <script>
            function fileListComponent({ instanceId, removeEvent, removeMap }) {
                return {
                    isPreviewOpen: false,
                    previewFile: null,
                    instanceId: instanceId || null,
                    removeEvent: removeEvent,
                    removeMap: removeMap || {},

                    openPreview(file) {
                        this.previewFile = file || null;
                        this.isPreviewOpen = !!this.previewFile;
                    },

                    closePreview() {
                        this.isPreviewOpen = false;
                        this.previewFile = null;
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

                    requestRemove(file) {
                        const removeId = file?.remove_id || this.removeMap?.[file?.url] || file?.id || null;
                        if (!removeId) {
                            return;
                        }

                        window.dispatchEvent(new CustomEvent(this.removeEvent, {
                            detail: {
                                instanceId: this.instanceId,
                                file: {
                                    id: removeId,
                                },
                            },
                        }));
                    },

                    isPreviewable(file) {
                        return this.isPdf(file) || this.isImage(file);
                    },

                    isPdf(file) {
                        const name = this.normalizeName(file);
                        return name.endsWith('.pdf');
                    },

                    isImage(file) {
                        const name = this.normalizeName(file);
                        return [
                            '.png',
                            '.jpg',
                            '.jpeg',
                            '.gif',
                            '.webp',
                            '.bmp',
                            '.tiff',
                            '.heic',
                            '.heif',
                        ].some(ext => name.endsWith(ext));
                    },

                    iconClass(file) {
                        const name = this.normalizeName(file);
                        if (name.endsWith('.pdf')) {
                            return 'fa-file-pdf';
                        }
                        if (
                            name.endsWith('.xls') ||
                            name.endsWith('.xlsx') ||
                            name.endsWith('.csv')
                        ) {
                            return 'fa-file-excel';
                        }
                        if (this.isImage(file)) {
                            return 'fa-file-image';
                        }
                        return 'fa-file';
                    },

                    normalizeName(file) {
                        const raw = (file?.file_name || file?.url || '').toString();
                        const sanitized = raw.split('?')[0];
                        return sanitized.toLowerCase();
                    },
                };
            }
        </script>
    @endpush
@endonce
