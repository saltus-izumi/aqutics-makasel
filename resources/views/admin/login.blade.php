<x-admin.guest-layout>
    <form method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <div class="tw:flex tw:justify-center tw:pt-[147px]">
            <div class="tw:w-[504px]">
                <div class="tw:text-[4.6rem] tw:font-bold tw:text-center">Log</div>
                <div class="tw:mb-[21px]">
                    ID
                    <x-form.input name="user_account" class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
                </div>
                <div class="tw:mb-[21px]">
                    PASS
                    <x-form.input name="user_password" class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
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
</x-admin.guest-layout>
