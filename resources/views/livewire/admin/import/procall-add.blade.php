<div class="tw:p-[26px]">
    <div class="tw:w-full">
        <div class="tw:mb-[21px]">
            <x-form.input-file name="procall_file" wire:model="procallFile" />
            @error('procallFile')
                <x-form.error-message>{{ $message }}</x-form.error-message>
            @enderror
        </div>
        <x-button.blue wire:click="import">取り込み</x-button.blue>
    </div>
</div>
