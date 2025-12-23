<x-admin.guest-layout>
    <div class="tw:flex tw:justify-center tw:items-center tw:h-screen">
        <div class="tw:w-[504px]">
            <div class="tw:text-[46pt] tw:font-bold tw:text-center">Log</div>
            <div class="tw:mb-[21px]">
                ID
                <x-form.input class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
            </div>
            <div class="tw:mb-[21px]">
                PASS
                <x-form.input class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
            </div>
            <div class="tw:mb-[21px]">
                <div class="tw:flex">
                    <x-form.checkbox name="" label="IDとPASSを記録する" />
                </div>
                <x-form.input class="tw:h-[63px] tw:bg-[#cfe2f3] tw:text-xl" />
            </div>
        </div>
    </div>
</x-admin.guest-layout>
