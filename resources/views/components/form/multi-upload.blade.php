@props([
    'name' => 'files',
    'maxFileCount' => 0,
    'maxFileSize' => 0,
    'allowMimeTypes' => [],
])

<div
    x-data="multiFileUpload({
        name: '{{ $name }}',
        maxFileCount: {{ $maxFileCount }},
        maxFileSize: {{ $maxFileSize }},
        allowMimeTypes: {{ json_encode($allowMimeTypes) }}
    })"
    class="tw:w-full"
>
    <!-- ドロップエリア -->
    <div
        @click="triggerFileInput"
        @drop.prevent="handleDrop($event)"
        @dragover.prevent
        @dragenter.prevent
        class="tw:p-2 tw:bg-[#ecf8fa9e] tw:border tw-border-zinc-300 tw-mb-2 tw:cursor-pointer tw:py-[20px] tw:flex tw:flex-col tw:items-center"
    >
        <i class="fa-solid fa-cloud-arrow-up tw:text-gray-500 tw:text-[20pt]"></i>
        <div class="tw:text-[10pt]">参照</div>
    </div>

    <!-- ファイル入力（非表示） -->
    <input
        type="file"
        multiple
        x-ref="fileInput"
        @change="handleFileSelect"
        class="tw:hidden"
    >

    <!-- ファイルリスト -->
    <div class="tw:flex tw:flex-col tw:gap-1">
        <template x-for="(file, index) in files" :key="index">
            <div class="tw:flex tw:items-center tw:px-2 tw:text-[11pt]">
                <!-- アイコン -->
                <div class="tw:w-4">
                    <template x-if="file.type.includes('image')">
                        <i class="far fa-file-image"></i>
                    </template>
                    <template x-if="file.type === 'application/pdf'">
                        <i class="far fa-file-pdf"></i>
                    </template>
                    <template x-if="file.type.includes('excel')">
                        <i class="far fa-file-excel"></i>
                    </template>
                    <template x-if="!['image','pdf','excel'].some(t => file.type.includes(t))">
                        <i class="far fa-file"></i>
                    </template>
                </div>

                <!-- ファイル名 -->
                <div class="tw-mr-[10px]" x-text="file.name"></div>

                <!-- 削除 -->
                <div class="tw:w-5 tw:text-right">
                    <button type="button" @click="removeFile(index)">
                        <i class="fa-solid fa-trash-can tw:text-red-500"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function multiFileUpload({ name, maxFileCount, maxFileSize, allowMimeTypes }) {
    return {
        files: [],

        triggerFileInput() {
            this.$refs.fileInput.click();
        },

        handleFileSelect(event) {
            const selectedFiles = Array.from(event.target.files);
            this.addFiles(selectedFiles);
            event.target.value = '';
        },

        handleDrop(event) {
            const dtFiles = Array.from(event.dataTransfer.files);
            this.addFiles(dtFiles);
        },

        addFiles(newFiles) {
            newFiles.forEach(file => {
                if (maxFileCount > 0 && this.files.length >= maxFileCount) {
                    alert('これ以上アップロードできません');
                    return;
                }
                if (maxFileSize > 0 && file.size > maxFileSize) {
                    alert(file.name + ' はサイズオーバーです');
                    return;
                }
                if (allowMimeTypes.length > 0 && !allowMimeTypes.includes(file.type)) {
                    alert(file.name + ' はアップロードできません');
                    return;
                }

                this.files.push(file);

                // ここでフォームに隠し input を作成
                this.$nextTick(() => {
                    const dt = new DataTransfer();
                    dt.items.add(file);

                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = name + '[]';
                    input.files = dt.files;
                    input.classList.add('tw:hidden');

                    // フォームを探して append
                    let form = this.$el.closest('form');
                    if (form) {
                        form.appendChild(input);
                    } else {
                        console.error('Form not found for file input');
                    }
                });
            });
        },

        removeFile(index) {
            // files 配列から削除
            this.files.splice(index, 1);

            // 対応する隠し input も削除
            const form = this.$el.closest('form');
            if (!form) return;

            const inputs = Array.from(form.querySelectorAll(`input[type="file"][name="${name}[]"]`));
            if (inputs[index]) {
                inputs[index].remove();
            }
        }
    }
}
</script>
