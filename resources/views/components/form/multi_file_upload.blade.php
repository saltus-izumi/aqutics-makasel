@props([
    'name' => 'files',
    'files' => [],
    'maxFileCount' => 0,
    'maxFileSize' => 0,
    'allowMimeTypes' => [],
    'uploadFunction' => null,
])

@php
    $existingFiles = collect($files ?? [])
        ->filter(fn ($file) => !($file instanceof \Illuminate\Http\UploadedFile))
        ->map(function ($file) {
            return [
                'id' => $file->id ?? null,
                'file_name' => $file->file_name ?? $file->name ?? '',
                'mime_type' => $file->mime_type ?? $file->type ?? '',
            ];
        })
        ->values();
@endphp

<div
    x-data="multiFileUploadComponent({
        name: '{{ $name }}',
        existingFiles: @json($existingFiles),
        maxFileCount: {{ $maxFileCount }},
        maxFileSize: @js($maxFileSize),
        allowMimeTypes: @json($allowMimeTypes),
        uploadFunction: @json($uploadFunction),
    })"
    class="tw:w-full"
>
    <div
        @click="triggerFileInput"
        @drop.prevent="handleDrop($event)"
        @dragover.prevent
        @dragenter.prevent
        @class([
            'tw:p-[10px] tw:bg-[#ecf8fa9e] tw:border tw:border-gray-300 tw:mb-[5px] tw:text-[#909090] tw:cursor-pointer tw:flex tw:items-center tw:justify-center',
            $attributes->get('class'),
        ])
    >
        <div>
            <i class="tw:text-[1.5rem] far fa-cloud-upload"></i>
            <div>参照</div>
        </div>
    </div>

    <input
        type="file"
        multiple
        x-ref="fileInput"
        @change="handleFileSelect"
        class="tw:hidden"
    >

    <div class="tw:flex tw:flex-col tw:gap-[3px]">
        <template x-for="(file, index) in existingFiles" :key="`existing-${index}`">
            <div
                class="tw:flex tw:text-[11pt] tw:px-[2px] tw:w-full"
                x-show="!file.markedDelete"
            >
                <div class="tw:w-[15px]">
                    <i class="far" :class="iconClass(file.mime_type)"></i>
                </div>
                <div class="tw:w-[calc(100%-35px)]">
                    <div class="tw:w-full tw:truncate" x-text="file.file_name"></div>
                </div>
                <div class="tw:w-[20px] tw:text-right">
                    <button type="button" class="tw:text-red-500" @click="removeExisting(index)">
                        <i class="fas fa-minus-circle"></i>
                    </button>
                    <input
                        type="checkbox"
                        class="tw:hidden"
                        :name="`${name}_delete[]`"
                        :value="file.id"
                        x-model="file.markedDelete"
                    >
                </div>
            </div>
        </template>

        <template x-for="(file, index) in newFiles" :key="`new-${index}`">
            <div class="tw:flex tw:text-[11pt] tw:px-[2px] tw:w-full">
                <div class="tw:w-[15px]">
                    <i class="far" :class="iconClass(file.file.type)"></i>
                </div>
                <div class="">
                    <div class="tw:w-full tw:truncate" x-text="file.file.name"></div>
                </div>
                <div class="tw:w-[20px] tw:text-right">
                    <button type="button" class="tw:text-red-500" @click="removeNew(index)">
                        <i class="fas fa-minus-circle"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div x-ref="hiddenInputs"></div>
</div>

<script>
function multiFileUploadComponent({ name, existingFiles, maxFileCount, maxFileSize, allowMimeTypes, uploadFunction }) {
    return {
        name,
        existingFiles: existingFiles.map(file => ({ ...file, markedDelete: false })),
        newFiles: [],
        maxFileSizeBytes: 0,

        init() {
            this.maxFileSizeBytes = this.parseSize(maxFileSize);
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
            files.forEach(file => {
                if (!this.canAddFile(file)) {
                    return;
                }

                const input = this.createHiddenInput(file);
                if (input) {
                    this.newFiles.push({ file, input });
                    this.callUploadHandler(file);
                }
            });
        },

        canAddFile(file) {
            const currentCount = this.existingFiles.filter(f => !f.markedDelete).length + this.newFiles.length;
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

        createHiddenInput(file) {
            const dt = new DataTransfer();
            dt.items.add(file);

            const input = document.createElement('input');
            input.type = 'file';
            input.name = `${name}[]`;
            input.files = dt.files;
            input.classList.add('tw:hidden');

            if (!this.$refs.hiddenInputs) {
                console.error('Hidden input container not found');
                return null;
            }

            this.$refs.hiddenInputs.appendChild(input);
            return input;
        },

        removeExisting(index) {
            if (!this.existingFiles[index]) return;
            this.existingFiles[index].markedDelete = true;
        },

        removeNew(index) {
            const target = this.newFiles[index];
            if (!target) return;
            if (target.input) {
                target.input.remove();
            }
            this.newFiles.splice(index, 1);
        },

        callUploadHandler(file) {
            if (!uploadFunction) return;
            const handler = window[uploadFunction];
            if (typeof handler === 'function') {
                handler(file, this);
            }
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
