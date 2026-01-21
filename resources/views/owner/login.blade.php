<x-owner.guest-layout>
    <form method="post" action="{{ route('owner.login.store') }}">
        @csrf
        <div class="tw:flex tw:justify-center tw:pt-[147px]">
            <div class="tw:w-[504px]">
                <div class="tw:text-[4.6rem] tw:font-bold tw:text-center">Log</div>
                <div class="tw:mb-[21px]">
                    ID
                    <x-form.input name="mail" class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
                    <x-form.error-message>{{ $errors->first('mail') }}</x-form.error-message>
                </div>
                <div class="tw:mb-[21px]">
                    PASS
                    <x-form.input type="password" name="password" class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
                    <x-form.error-message>{{ $errors->first('password') }}</x-form.error-message>
                </div>
                <div class="tw:mb-[21px]">
                    <div class="tw:flex">
                        <x-form.checkbox name="remember">IDとPASSを記憶する</x-form.checkbox>
                    </div>
                    <x-button.blue type="submit" class="tw:text-[1.8rem] tw:w-full tw:h-[63px]">ログイン</x-button.blue>
                </div>
            </div>
        </div>
    </form>
</x-owner.guest-layout>
