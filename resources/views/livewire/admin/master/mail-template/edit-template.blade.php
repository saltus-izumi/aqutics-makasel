<form wire:submit.prevent="save" class="tw:px-[20px] tw:w-fit">
    <div class="tw:border-b tw:pb-[21px] tw:min-h-[72px] tw:flex tw:items-end">
        <div class="tw:w-[532px]">
            <div class="tw:pb-1">
                メール種別<x-badge.red class="tw:ml-1">必須</x-badge.red>
            </div>
            <x-form.select name="mail_kind" wire:model.live="mailKind" class="tw:!h-[42px] tw:text-[1.2rem]" :options="App\Models\MailTemplate::MAIL_KIND" :value="$mailKind" empty=" " :is_error="$errors->has('mailKind')" />
            <x-form.error-message>{{ $errors->first('mailKind') }}</x-form.error-message>
        </div>
    </div>

    <div class="tw:pt-[21px] tw:flex tw:gap-x-[38px]">
        <div class="tw:w-[780px] tw:flex tw:flex-col tw:gap-y-[21px]">
            <div>
                <div class="tw:pb-1">
                    件名<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.input name="subject" wire:model="subject" class="tw:text-[1.2rem] tw:!h-[42px]" :is_error="$errors->has('subject')" />
                <x-form.error-message>{{ $errors->first('subject') }}</x-form.error-message>
            </div>
            <div>
                <div class="tw:pb-1">
                    本文<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <x-form.textarea name="body" wire:model="body" class="tw:text-[1.2rem] tw:h-[420px]">{{ $body }}</x-form.textarea>
                <x-form.error-message>{{ $errors->first('body') }}</x-form.error-message>
            </div>
        </div>

        <div class="tw:w-[390px]">
            <div class="tw:pb-1">置換文字列</div>
            <div class="tw:min-h-[468px] tw:flex tw:flex-col tw:gap-y-[12px] tw:p-[16px] tw:border tw:border-[#d9d9d9] tw:bg-[#fafafa] tw:text-[1.1rem]">
                <div class="tw:flex tw:items-center">
                    <div class="tw:w-[120px] tw:font-semibold">物件名</div>
                    <div>##investment_name##</div>
                </div>
                <div class="tw:flex tw:items-center">
                    <div class="tw:w-[120px] tw:font-semibold">部屋番号</div>
                    <div>##room_no##</div>
                </div>
                <div class="tw:flex tw:items-center">
                    <div class="tw:w-[120px] tw:font-semibold">工事会社</div>
                    <div>##trading_company##</div>
                </div>
            </div>
        </div>
    </div>

    <div class="tw:mt-[21px] tw:flex tw:justify-end">
        <x-button.blue type="submit">登録</x-button.blue>
    </div>
</form>
