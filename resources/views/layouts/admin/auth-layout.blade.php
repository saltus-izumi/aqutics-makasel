@props([
    'title' => 'PM Log',
    'class' => '',
])
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <div class="tw:min-h-screen" x-data="menu()">
        <div class="tw:fixed tw:top-0 tw:w-[300px] tw:transition-[top,height,left,padding] tw:duration-600 tw:z-10"
            :class="[
                float ? 'tw:top-[48px] tw:pt-[21px] tw:h-[calc(100vh-138px)]' : 'tw:h-screen',
                (float && !sidebarHover) ? 'tw:-left-[300px]' : 'tw:left-0'
            ]"
            @mouseenter="sidebarHover = true"
            @mouseleave="sidebarHover = false"
        >
            <div class="tw:border-r tw:flex tw:flex-col tw:h-full"
                :class="float ? 'tw:border-b tw:rounded-xl tw:overflow-hidden' : ''"
            >
                <div class="tw:bg-black tw:h-[48px] tw:p-1 tw:flex tw:items-center tw:justify-between">
                    <img src="{{ url('/images/admin-logo.png') }}" class="tw:h-full">
                    <div class="tw:pr-[10px]" x-show="!float">
                        <i class="far fa-angle-double-left tw:text-[2.3rem] tw:text-white tw:cursor-pointer" @click="toggleFloat()"></i>
                    </div>
                </div>
                <div class="tw:px-[20px]">
                    <ul class="tw:pb-[20px] tw:border-b">
                        <li class="tw:leading-[42px] tw:text-[1.4rem] tw:font-bold">目標ダッシュボード</li>
                        <li class="tw:leading-[42px] tw:text-[1.4rem] tw:font-bold">KPIダッシュボード</li>
                        <li class="tw:leading-[42px] tw:text-[1.4rem] tw:font-bold">
                            在庫リスト｜担当<span class="tw:bg-red-600 tw:text-white tw:rounded-full tw:ml-[20px] tw:p-1">15</span>
                        </li>
                    </ul>
                </div>
                <div class="tw:flex-1 tw:overflow-y-auto tw:p-[20px]">
                    <ul class="tw:pb-[20px]">
                        <x-layout.side-menu-item title="委託｜ガイドライン"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="Log｜ガイドライン"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="職別｜ガイドライン"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="インポート">
                            <ul>
                                <li><a href="/app/admin/CsvImports/10">物件情報</a></li>
                                <li><a href="/app/admin/CsvImports/1">賃貸革命（募集一覧）</a></li>
                                <li><a href="/app/admin/CsvImports/2">SUUMO</a></li>
                                <li><a href="/app/admin/CsvImports/3">athome</a></li>
                                <li><a href="/app/admin/CsvImports/4">不動産BB</a></li>
                                <li><a href="/app/admin/CsvImports/5">イタンジ（内見予約くん）</a></li>
                                <li><a href="/app/admin/CsvImports/7">個人申込</a></li>
                                <li><a href="/app/admin/CsvImports/8">法人申込</a></li>
                                <li><a href="/app/admin/CsvImports/6">PMView（新規）</a></li>
                                <li><a href="/app/admin/CsvImports/11">PMView（更新）</a></li>
                                <li><a href="/app/admin/CsvImports/9">入居者状況一覧</a></li>
                                <li><a href="/app/admin/CsvImports/12">解約予定一覧</a></li>
                                <li><a href="/app/admin/CsvImports/13">オーナー情報</a></li>
                                <li><a href="/app/admin/CsvImports/14">貸主情報</a></li>
                                <li><a href="/app/admin/CsvImports/15">貸主口座情報</a></li>
                                <li><a href="/app/admin/CsvImports/16">解約精算一覧</a></li>
                                <li><a href="/app/admin/CsvImports/17">入金・送金一覧</a></li>
                                <li><a href="/app/admin/CsvImports/incomes-expenses">収支</a></li>
                            </ul>
                        </x-layout.side-menu-item>
                        <x-layout.side-menu-item title="特約業務一覧"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="ステークホルダー"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="FAQ"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="チャット設定"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="マスタ登録"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="アカウント管理"></x-layout.side-menu-item>
                        <x-layout.side-menu-item title="Master">
                            <ul>
                                <li class="tw:leading-[30px]"><a href="/app/admin/CsvImports/10">物件情報</a></li>
                            </ul>
                        </x-layout.side-menu-item>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tw:fixed tw:top-0 tw:left-[300px] tw:w-[calc(100vw-300px)] tw:min-h-screen tw:transition-all tw:duration-600"
            :class="float ? 'tw:!left-0 tw:!w-screen' : ''"
        >
            <div class="tw:bg-black tw:h-[48px] tw:w-full tw:flex tw:items-center tw:text-white"
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
                <div class="tw:w-[222px] tw:px-2 tw:text-[1.4rem]">
                    {{ $title }}
                </div>
                <x-layout.top-menu-item title="チャット"/>
                <x-layout.top-menu-item title="オペレーション"
                    :subItems="[
                        'http://local.zen.inc/app/admin/operations/add' => 'オペレーション作成'
                    ]"
                />
                <x-layout.top-menu-item title="物件管理" />
                <x-layout.top-menu-item title="物件詳細" />
            </div>
            <div class="{{ $class }}">
                {{ $slot }}
            </div>
        </div>
    </div>
    @livewireScripts
    <script>
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
</body>
</html>
