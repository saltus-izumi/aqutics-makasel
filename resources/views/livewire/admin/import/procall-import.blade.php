<div class="tw:p-[26px]">
    <div class="tw:w-full">
        <div class="tw:mb-[21px]">
            <x-form.input-file name="procall_file" wire:model="procallFile" />
            @error('procallFile')
                <x-form.error-message>{{ $message }}</x-form.error-message>
            @enderror
        </div>
        <x-button.blue wire:click="import">取り込み</x-button.blue>
        <div class="tw:mt-[21px]">
            @if ($insertCount !== null && $errorCount === 0)
                <div>新規登録件数：{{ $insertCount }}件</div>
            @endif
            @if ($errorCount !== null && $errorCount > 0)
                <div class="tw:text-[#ff5555]"><span class="tw:font-bold">取り込みに失敗しました。</span>エラー件数：{{ $errorCount }}件</div>
            @endif
        </div>
        @if ($errorMessages)
        <div class="tw:border-[4px] tw:border-[#ff9999] tw:rounded-md tw:p-2 tw:text-[#ff5555]">
            @foreach($errorMessages as $errorMessage)
                {{ $errorMessage }}<br>
            @endforeach
        </div>
        @endif
    </div>
</div>
