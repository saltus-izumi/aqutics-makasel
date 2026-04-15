<div
    class="tw:p-[26px]"
    x-data
    x-on:open-individual-tenancy-application-import-loading-modal.window="document.body.classList.add('tw:cursor-wait')"
    x-on:close-individual-tenancy-application-import-loading-modal.window="document.body.classList.remove('tw:cursor-wait')"
>
        <div class="tw:w-full">
            <div class="tw:mb-[3px]">
                CSVファイル名：<span class="tw:font-bold">個人申込-YYYYMMDD.csv</span>
            </div>
        <div class="tw:mb-[21px]">
            <x-form.input-file name="individual_tenancy_application_file" wire:model="individualTenancyApplicationFile" />
            @error('individualTenancyApplicationFile')
                <x-form.error-message>{{ $message }}</x-form.error-message>
            @enderror
        </div>
        <x-button.blue
            type="button"
            wire:click="import"
            wire:loading.attr="disabled"
            wire:target="import"
            x-on:click="window.dispatchEvent(new CustomEvent('open-individual-tenancy-application-import-loading-modal'))"
        >
            取り込み
        </x-button.blue>
        <div class="tw:mt-[21px]">
            @if ($errorCount === 0)
                <div class="tw:text-[#2e7d32]">
                    <span class="tw:font-bold">取り込みに成功しました。</span>
                    <div class="tw:pl-[0.5rem]">
                        読み込み件数：{{ $readCount }}件<br>
                        入居者新規件数：{{ $insertResidentCount }}件<br>
                        入居者更新件数：{{ $updateResidentCount }}件
                    </div>
                </div>
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

    <x-modal title="取込中です" event="individual-tenancy-application-import-loading-modal">
        <div class="tw:text-[1.5rem] tw:leading-[1.8]">
            取込中です。しばらくお待ちください。
        </div>
    </x-modal>
</div>
