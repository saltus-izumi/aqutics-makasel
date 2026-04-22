<form wire:submit.prevent="save" class="">
    <div class="tw:flex tw:gap-x-[21px]">
        <div class="tw:w-[780px]">
            <div class="tw:flex">
                <div class="tw:w-[260px] tw:min-h-[42px] tw:leading-[42px] tw:text-left tw:px-[21px] tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">
                    メール種別<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <div class="tw:w-[520px] tw:min-h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-l-0">
                    <x-form.select name="mail_kind" wire:model.live="mailKind" class="tw:!h-[40px]" :border="false" :options="App\Models\MailTemplate::MAIL_KIND" :value="$mailKind" empty=" " :is_error="$errors->has('mailKind')" />
                    <x-form.error-message>{{ $errors->first('mailKind') }}</x-form.error-message>
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[260px] tw:min-h-[42px] tw:leading-[42px] tw:text-left tw:px-[21px] tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">
                    件名<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <div class="tw:w-[520px] tw:min-h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.input name="subject" wire:model="subject" class="tw:!h-[40px]" :border="false" :is_error="$errors->has('subject')" />
                    <x-form.error-message>{{ $errors->first('subject') }}</x-form.error-message>
                </div>
            </div>
            <div class="tw:flex">
                <div class="tw:w-[260px] tw:min-h-[420px] tw:leading-[42px] tw:text-left tw:px-[21px] tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">
                    本文<x-badge.red class="tw:ml-1">必須</x-badge.red>
                </div>
                <div class="tw:w-[520px] tw:min-h-[420px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
                    <x-form.textarea name="body" wire:model="body" class="tw:!h-[418px]" :border="false">{{ $body }}</x-form.textarea>
                    <x-form.error-message>{{ $errors->first('body') }}</x-form.error-message>
                </div>
            </div>
        </div>
        <div class="tw:w-[390px]">
            <div class="tw:w-[390px] tw:h-[42px] tw:leading-[42px] tw:text-left tw:px-[21px] tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">置換文字列</div>
            <div class="tw:w-[390px] tw:h-[462px] tw:flex tw:flex-col tw:gap-y-[10px] tw:p-[21px] tw:border tw:border-[#cccccc] tw:border-t-0">
                <div class="tw:flex">
                    <div class="tw:w-[130px]">物件名</div>
                    <div>##investment_name##</div>
                </div>
                <div class="tw:flex">
                    <div class="tw:w-[130px]">部屋番号</div>
                    <div>##room_no##</div>
                </div>
                <div class="tw:flex">
                    <div class="tw:w-[130px]">工事会社</div>
                    <div>##trading_company##</div>
                </div>
            </div>
        </div>
    </div>
    <div class="tw:w-[1191px] tw:mt-[21px] tw:flex tw:justify-end">
        <x-button.blue type="submit">登録</x-button.blue>
    </div>
</form>
