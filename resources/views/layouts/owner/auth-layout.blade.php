@props([
    'title' => 'PM Log',
    'class' => '',
])
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="tw:min-h-screen" x-data="menu()">
        <div class="tw:fixed tw:top-0 tw:w-[318px] tw:transition-[top,height,left,padding] tw:duration-600 tw:z-10"
            :class="[
                float ? 'tw:top-[63px] tw:pt-[21px] tw:h-[calc(100vh-138px)]' : 'tw:h-screen',
                (float && !sidebarHover) ? 'tw:-left-[318px]' : 'tw:left-0'
            ]"
            @mouseenter="sidebarHover = true"
            @mouseleave="sidebarHover = false"
        >
            <div class="tw:border-r tw:flex tw:flex-col tw:h-full tw:bg-white"
                :class="float ? 'tw:border-b tw:rounded-xl tw:overflow-hidden' : ''"
            >
                <div class="tw:bg-black tw:h-[63px] tw:p-1 tw:flex tw:items-center tw:justify-between">
                    <div>
                        <img src="{{ url('/images/admin-logo.png') }}" class="tw:h-[42px]">
                    </div>
                    <div class="tw:pr-[10px]" x-show="!float">
                        <i class="far fa-angle-double-left tw:text-[2.3rem] tw:text-white tw:cursor-pointer" @click="toggleFloat()"></i>
                    </div>
                </div>
                <div class="tw:px-[20px] tw:py-[21px] tw:h-[170px] tw:flex tw:flex-col tw:justify-around tw:border-b">
                    <div class="tw:text-[1.7rem] tw:font-bold">
                        お知らせ
                        <span class="tw:bg-red-600 tw:text-white tw:rounded-full tw:ml-[20px] tw:p-1">15</span>
                    </div>
                    <div class="tw:text-[1.7rem] tw:font-bold">
                        やることリスト
                    </div>
                    {{-- <ul class="tw:pb-[20px]">
                        <li class="tw:mb-[21px] tw:leading-[42px] tw:text-[1.7rem] tw:font-bold">
                            お知らせ
                            <span class="tw:bg-red-600 tw:text-white tw:rounded-full tw:ml-[20px] tw:p-1">15</span>
                        </li>
                        <li class="tw:leading-[42px] tw:text-[1.7rem] tw:font-bold">やることリスト</li>
                    </ul> --}}
                </div>
                <div class="tw:flex-1 tw:overflow-y-auto tw:p-[20px]">
                    <div class="tw:pb-[20px]">
                        <x-button.blue class="tw:w-full tw:text-[1.6rem] tw:font-bold">すべての物件</x-button.blue>
                        <ul class="tw:mt-[21px]">
                            @foreach ($investments as $investment)
                                <li class="tw:leading-[42px] tw:flex">
                                    <div class="tw:flex-1 tw:truncate tw:text-[1.3rem] ">
                                        {{ $investment->investment_name }}
                                    </div>
                                    <div class="tw:w-[26px] tw:text-center">
                                        {{ $investment->investmentRooms->count() }}
                                    </div>
                                    <div class="tw:w-[26px] tw:text-center">
                                        V
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tw:fixed tw:top-0 tw:left-[318px] tw:w-[calc(100vw-318px)] tw:min-h-screen tw:transition-all tw:duration-600"
            :class="float ? 'tw:!left-0 tw:!w-screen' : ''"
        >
            <div class="tw:bg-black tw:h-[63px] tw:w-full tw:flex tw:items-center tw:text-white"
                @mouseenter="sidebarHover = true"
                @mouseleave="sidebarHover = false"
            >
                <div class="tw:w-[78px] tw:text-center" x-show="float" x-cloak x-data="{ hover: false }">
                    <i class="tw:text-[2.3rem] tw:cursor-pointer far"
                        :class="hover ? 'fa-angle-double-right' : 'fa-bars'"
                        @mouseenter="hover = true"
                        @mouseleave="hover = false"
                        @click="toggleFloat()"
                    ></i>
                </div>
                <div class="tw:w-[222px] tw:px-2 tw:text-[1.4rem]" x-show="float" x-cloak>
                    {{ $title }}
                </div>
                <x-layout.top-menu-item title="チャット"/>
                <x-layout.top-menu-item title="オペレーション"
                    :subItems="[
                        route('owner.operation.index') => 'オペレーション一覧',
                        route('admin.operation.create') => 'オペレーション作成',
                    ]"
                />
                <x-layout.top-menu-item title="物件管理" />
                <x-layout.top-menu-item title="物件詳細" />
            </div>
            <div
                @class([
                    'tw:h-[calc(100vh-63px)]' => !str_contains($class, 'tw:h-'),
                    $class
                ])
            >
                {{ $slot }}
            </div>
        </div>
    </div>
    @livewireScripts
    <script>
        // Livewireのエンドポイントをサブディレクトリに対応（fetchを上書き）
        (function() {
            const appDir = '{{ env('APP_DIR', '') }}';
            if (appDir) {
                const originalFetch = window.fetch;
                window.fetch = function(url, options) {
                    if (typeof url === 'string' && url === '/livewire/update') {
                        url = '/' + appDir + '/livewire/update';
                    }
                    return originalFetch.call(this, url, options);
                };
            }
        })();

        function menu() {
            return {
                float: false,
                sidebarHover: false,
                toggleFloat() {
                    this.float = !this.float;
                },
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
