<div>
    <div class="tw:flex tw:h-[42px] tw:items-end">
        <div class="tw:w-[806px] tw:text-[1.2rem] tw:font-bold">仲介会社（イタンジID：{{ $enProgress->broker?->itanji_id }}）</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[260px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">商号</div>
        <div class="tw:w-[234px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">登録番号</div>
        <div class="tw:w-[312px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">住所</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[260px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_name }}</div>
        </div>
        <div class="tw:w-[234px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div></div>
        </div>
        <div class="tw:w-[312px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_address }}</div>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">担当名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">担当電話番号</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">メールアドレス</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">TEL</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">FAX</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_tantou_name }}</div>
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_mobile_tel }}</div>
        </div>
        <div class="tw:w-[286px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_mail }}</div>
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_tel }}</div>
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:flex tw:items-center tw:px-1">
            <div>{{ $enProgress->broker?->broker_fax }}</div>
        </div>
    </div>
</div>
