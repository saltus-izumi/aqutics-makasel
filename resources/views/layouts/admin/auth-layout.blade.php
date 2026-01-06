@props([
    'title' => 'PM Log',
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
    <div class="tw:min-h-screen tw:flex">
        <div class="tw:w-[300px] tw:border-r tw:h-screen tw:flex tw:flex-col">
            <div class="tw:bg-black tw:h-[48px] tw:p-1 tw:flex tw:items-center tw:justify-between">
                <img src="{{ url('/images/admin-logo.png') }}" class="tw:h-full">
                <div class="tw:pr-[10px]">
                    <i class="far fa-angle-double-left tw:text-[2.3rem] tw:text-white tw:cursor-pointer"></i>
                </div>
            </div>
            <div class="tw:p-[20px]">
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
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>委託｜ガイドライン</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>Log｜ガイドライン</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>職別｜ガイドライン</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>インポート</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>特約業務一覧</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>ステークホルダー</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>FAQ</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>チャット設定</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>マスタ登録</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>アカウント管理</li>
                    <li class="tw:leading-[42px] tw:text-[1.4rem]"><i class="far fa-angle-right tw:pr-3"></i>Master</li>
                </ul>
            </div>
        </div>
        <div class="tw:flex-1">
            <div class="tw:bg-black tw:h-[48px] tw:w-full">
            </div>
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>
