<div class="tw:p-[26px]">
    <div class="tw:w-full">
        <div class="tw:mb-[3px]">
            CSVファイル名：<span class="tw:font-bold">個人申込-YYYYMMDD.csv</div>
        </div>
        <div class="tw:mb-[21px]">
            <x-form.input-file name="individual_tenancy_application_file" wire:model="individualTenancyApplicationFile" />
            @error('individualTenancyApplicationFile')
                <x-form.error-message>{{ $message }}</x-form.error-message>
            @enderror
        </div>
        <x-button.blue wire:click="import">取り込み</x-button.blue>
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
</div>
