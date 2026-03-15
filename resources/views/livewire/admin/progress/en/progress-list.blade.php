<div
    class="tw:relative"
    x-data="tablePopup(@js($sortOrder ?? 'asc'), @js($sortField ?? 'id'), @js($filters ?? []))"
    x-on:click="handleClick($event)"
    x-on:input="handleDateInput($event)"
    x-on:calendar-input.window="handleCalendarInput($event)"
    x-on:keydown.escape.window="closeAll()"
>
    <table class="tw:table-fixed tw:w-[1716px] tw:min-w-[1716px]">
        <colgroup>
            <col class="tw:w-[52px]">       {{-- ENID --}}
            <col class="tw:w-[52px]">       {{-- 物件ID --}}
            <col class="tw:w-[182px]">      {{-- 物件名 --}}
            <col class="tw:w-[52px]">       {{-- 号室 --}}
            <col class="tw:w-[52px]">       {{-- 番手 --}}
            <col class="tw:w-[104px]">      {{-- 入居者名 --}}
            <col class="tw:w-[52px]">       {{-- 完工予定日 --}}
            <col class="tw:w-[52px]">       {{-- 完工日 --}}
            <col class="tw:w-[52px]">       {{-- 始期日 --}}
            <col class="tw:w-[104px]">      {{-- 仲介会社 --}}
            <col class="tw:w-[78px]">       {{-- 責任者 --}}
            <col class="tw:w-[78px]">       {{-- 実行者 --}}
            <col class="tw:w-[182px]">      {{-- ネクストアクション --}}
            <col class="tw:w-[52px]">       {{-- 申込日 --}}
            <col class="tw:w-[52px]">       {{-- 保証審査 --}}
            <col class="tw:w-[52px]">       {{-- WP審査 --}}
            <col class="tw:w-[52px]">       {{-- OWN報告 --}}
            <col class="tw:w-[52px]">       {{-- OWN承諾 --}}
            <col class="tw:w-[52px]">       {{-- 始期日確定日 --}}
            <col class="tw:w-[52px]">       {{-- 鍵依頼日 --}}
            <col class="tw:w-[52px]">       {{-- 請求発行 --}}
            <col class="tw:w-[52px]">       {{-- 契約発送 --}}
            <col class="tw:w-[52px]">       {{-- 契約入金 --}}
            <col class="tw:w-[52px]">       {{-- 契約改修 --}}
            <col class="tw:w-[52px]">       {{-- 電気解約 --}}
            <col class="tw:w-[52px]">       {{-- 鍵渡し --}}
            <col class="tw:w-[52px]">       {{-- 書類格納 --}}
            <col class="tw:w-[52px]">       {{-- 完了報告 --}}
            <col class="tw:w-[52px]">       {{-- 完了 --}}
        </colgroup>
        <thead class="tw:sticky tw:top-0 tw:z-10">
            <tr class="tw:h-[21px] tw:bg-white">
                <td class="tw:sticky tw:left-[52px] tw:bg-white" rowspan="2" colspan="2">
                    <x-button.blue class="tw:!h-[21px] tw:!w-[104px] tw:!font-normal">検索</x-button.blue>
                </td>
                <td class="tw:sticky tw:left-[156px] tw:pl-[10px] tw:bg-white" rowspan="2">
                    案件数  {{ $enProgresses->count() }}
                    <button type="button" class="tw:ml-2 tw:text-xs tw:px-2 tw:py-0.5 tw:border tw:rounded tw:cursor-pointer" x-on:click="clearAllFilters()">フィルタークリア</button>
                </td>
                <td class="tw:sticky tw:left-[338px] tw:bg-white" rowspan="2"></td>
                <td rowspan="2" colspan="9"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">KPI_LT</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">3日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center"></td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center"></td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center"></td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center"></td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">20日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
            </tr>
            <tr class="tw:h-[50px]">
                <td class="tw:sticky tw:left-[52px] tw:text-center tw:bg-[#efefef]" rowspan="2">ENID</td>
                <td class="tw:sticky tw:left-[104px] tw:text-center tw:bg-[#efefef]" rowspan="2">物件ID</td>
                <td class="tw:sticky tw:left-[156px] tw:text-center tw:bg-[#efefef]" rowspan="2">物件名</td>
                <td class="tw:sticky tw:left-[338px] tw:text-center tw:bg-[#efefef]" rowspan="2">号室</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">番手</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">入居者名</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">完工<br>予定日</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">完工<br>日</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">始期日</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">仲介会社</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">責任者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">実行者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">ネクストアクション</td>
                <td class="tw:text-center tw:bg-black tw:text-white">申込日</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">保証<br>審査</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">WP<br>審査</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">OWN<br>報告</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">OWN<br>承諾</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">始期日<br>確定日</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">鍵<br>依頼日</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">請求<br>発行</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">契約<br>発送</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">契約<br>入金</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">契約<br>回収</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">電気<br>解約</td>
                <td class="tw:text-center tw:bg-[#efefef]">鍵渡し</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">書類<br>格納</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完了<br>報告</td>
                <td class="tw:text-center tw:bg-black tw:text-white">完了</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">実質LT</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['move_out'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['cost_received'] ?? 'ー' }}</td>
                <td class="tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['power_activation_date'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['tenant_burden_confirmed'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['owner_proposed'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['owner_approved'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['ordered'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['completion_scheduled'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['completion_received'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['completion_reported'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['kakumei_registered'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['complete'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['complete'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['complete'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['complete'] ?? 'ー' }}</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:sticky tw:left-[52px] tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="ENID"
                        data-sort-field="progress_id"
                        data-filter-field="progress_id"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('id')])
                    >▼</div>
                </td>
                <td class="tw:sticky tw:left-[104px] tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="物件ID"
                        data-sort-field="investment_id"
                        data-filter-field="investment_id"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('investment_id')])
                    >▼</div>
                </td>
                <td class="tw:sticky tw:left-[156px] tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="物件名"
                        data-sort-field="investment_name"
                        data-filter-field="investment_name"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('investment_name')])
                    >▼</div>
                </td>
                <td class="tw:sticky tw:left-[338px] tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="号室"
                        data-sort-field="investment_room_number"
                        data-filter-field="investment_room_number"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('investment_room_number')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="番手"
                        data-sort-field="priority_order"
                        data-filter-field="priority_order"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('priority_order')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="入居者名"
                        data-sort-field="responsible_user_id"
                        data-filter-field="responsible_user_id"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($genpukuResponsibleOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('responsible_user_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完工予定日"
                        data-sort-field="completion_scheduled_date"
                        data-filter-field="completion_scheduled_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('completion_scheduled_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完工日"
                        data-sort-field="executor_user_id"
                        data-filter-field="executor_user_id"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('executor_user_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="始期日"
                        data-sort-field="start_date"
                        data-filter-field="start_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('start_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="仲介会社"
                        data-sort-field="responsible_user_id"
                        data-filter-field="responsible_user_id"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($genpukuResponsibleOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('responsible_user_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="責任者"
                        data-sort-field="responsible_user_id"
                        data-filter-field="responsible_user_id"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($genpukuResponsibleOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('responsible_user_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="実行者"
                        data-sort-field="executor_user_id"
                        data-filter-field="executor_user_id"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($genpukuResponsibleOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('executor_user_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="ネクストアクション"
                        data-sort-field="next_action"
                        data-filter-field="next_action"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($nextActionOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('next_action')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="申込日"
                        data-sort-field="application_date"
                        data-filter-field="application_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('application_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="保証審査"
                        data-sort-field="guarantee_screening_date"
                        data-filter-field="guarantee_screening_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('guarantee_screening_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="WP審査"
                        data-sort-field="wp_screening_date"
                        data-filter-field="wp_screening_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('wp_screening_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="OWN報告"
                        data-sort-field="owner_reported_date"
                        data-filter-field="owner_reported_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('owner_reported_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="OWN承諾"
                        data-sort-field="owner_approved_date"
                        data-filter-field="owner_approved_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('owner_approved_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="始期日確定日"
                        data-sort-field="start_date_confirmed_date"
                        data-filter-field="start_date_confirmed_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('start_date_confirmed_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="鍵依頼日"
                        data-sort-field="key_requested_date"
                        data-filter-field="key_requested_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('key_requested_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="請求発行"
                        data-sort-field="invoice_issued_date"
                        data-filter-field="invoice_issued_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('invoice_issued_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="契約発送"
                        data-sort-field="contract_sent_date"
                        data-filter-field="contract_sent_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('contract_sent_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="契約入金"
                        data-sort-field="contract_payment_date"
                        data-filter-field="contract_payment_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('contract_payment_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="契約回収"
                        data-sort-field="contract_collected_date"
                        data-filter-field="contract_collected_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('contract_collected_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="電気解約"
                        data-sort-field="electricity_cancellation_date"
                        data-filter-field="electricity_cancellation_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('electricity_cancellation_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="鍵渡し"
                        data-sort-field="key_handover_date"
                        data-filter-field="key_handover_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('key_handover_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="書類格納"
                        data-sort-field="documents_archived_date"
                        data-filter-field="documents_archived_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('documents_archived_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完了報告"
                        data-sort-field="completion_reported_date"
                        data-filter-field="completion_reported_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('completion_reported_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完了"
                        data-sort-field="completed_date"
                        data-filter-field="completed_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('completed_date')])
                    >▼</div>
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($enProgresses as $enProgress)
                @php
                    $isReProposeOrCancel = in_array($enProgress->next_action, [
                        App\Models\EnProgress::NEXT_ACTION_RE_PROPOSED,
                        App\Models\EnProgress::NEXT_ACTION_CANCEL,
                    ], true);
                    $stickyCellBgClass = $isReProposeOrCancel ? 'tw:bg-[#efefef]' : 'tw:bg-white';
                @endphp
                <tr @class([
                    'tw:h-[42px] tw:border-b tw:border-b-[#cccccc]',
                    'tw:bg-[#efefef]' => $isReProposeOrCancel
                ]) data-popup-disabled="{{ $isReProposeOrCancel ? '1' : '0' }}">
                    <td class="tw:sticky tw:left-[52px] tw:z-[1] tw:text-center {{ $stickyCellBgClass }}">
                        <a href="{{ route('admin.progress.en.detail', ['enProgressId' => $enProgress->id]) }}" class="tw:text-pm_blue_001">
                            {{ $enProgress->id . ($enProgress->reproposal_count > 0 ? "-{$enProgress->reproposal_count}" : '' )  }}
                        </a>
                    </td>
                    <td class="tw:sticky tw:left-[104px] tw:z-[1] tw:text-center {{ $stickyCellBgClass }}">{{ $enProgress->progress->investment_id }}</td>
                    <td class="tw:sticky tw:left-[156px] tw:z-[1] {{ $stickyCellBgClass }}">{{ $enProgress->progress?->investment?->investment_name }}</td>
                    <td class="tw:sticky tw:left-[338px] tw:z-[1] tw:text-center {{ $stickyCellBgClass }}">{{ $enProgress->progress?->investment_room_uid == 0 ? '共用部' : $enProgress->progress?->investmentRoom?->investment_room_number }}</td>
                    <td class="tw:text-center tw:px-[3px]">{{ $enProgress->priority_order }}</td>
                    <td class="tw:text-center tw:px-[3px]">{{ $enProgress->firstEnProgressOccupant?->full_name }}</td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-tooltip :text="$enProgress?->progress?->latestGeProgress?->completion_scheduled_date?->format('Y/m/d')">
                            {{ $enProgress?->progress?->latestGeProgress?->completion_scheduled_date?->format('m/d') }}
                        </x-tooltip>
                    </td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-tooltip :text="$enProgress?->progress?->latestGeProgress?->completion_scheduled_date?->format('Y/m/d')">
                            {{ $enProgress?->progress?->latestGeProgress?->completion_scheduled_date?->format('m/d') }}
                        </x-tooltip>
                    </td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-tooltip :text="$enProgress?->start_date?->format('Y/m/d')">
                            {{ $enProgress?->progress?->start_date?->format('m/d') }}
                        </x-tooltip>
                    </td>
                    <td class="tw:text-center tw:px-[3px]">{{ $enProgress->broker?->broker_name }}</td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-form.select
                            name="responsible_user_id"
                            :options="$enResponsibleShortOptions"
                            empty="　"
                            :value="$enProgress->responsible_user_id"
                            wire:input="updateSelectValue({{ $enProgress->id }}, 'responsible_user_id', $event.target.value)"
                            :disabled="$isReProposeOrCancel"
                            class="tw:disabled:bg-[#efefef]"
                        />
                    </td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-form.select
                            name="executor_user_id"
                            :options="$enResponsibleShortOptions"
                            empty="　"
                            :value="$enProgress->executor_user_id"
                            wire:input="updateSelectValue({{ $enProgress->id }}, 'executor_user_id', $event.target.value)"
                            :disabled="$isReProposeOrCancel"
                            class="tw:disabled:bg-[#efefef]"
                        />
                    </td>
                    <td class="tw:text-center">{{ App\Models\EnProgress::NEXT_ACTIONS[$enProgress?->next_action] ?? '' }}</td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="申込日"
                            data-popup-date="{{ $enProgress?->application_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="application_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="application_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="保証審査"
                            data-popup-date="{{ $enProgress?->guarantee_screening_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="guarantee_screening_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="guarantee_screening_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="WP審査"
                            data-popup-date="{{ $enProgress?->wp_screening_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="wp_screening_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="wp_screening_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="OWN報告"
                            data-popup-date="{{ $enProgress?->owner_reported_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="owner_reported_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="owner_reported_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="OWN承諾"
                            data-popup-date="{{ $enProgress?->owner_approved_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="owner_approved_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="owner_approved_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="始期日確定日"
                            data-popup-date="{{ $enProgress?->start_date_confirmed_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="start_date_confirmed_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="start_date_confirmed_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="鍵依頼日"
                            data-popup-date="{{ $enProgress?->key_requested_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="key_requested_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="key_requested_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="請求発行"
                            data-popup-date="{{ $enProgress?->invoice_issued_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="invoice_issued_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="invoice_issued_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="契約発送"
                            data-popup-date="{{ $enProgress?->contract_sent_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="contract_sent_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="contract_sent_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="契約入金"
                            data-popup-date="{{ $enProgress?->contract_payment_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="contract_payment_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="contract_payment_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="契約回収"
                            data-popup-date="{{ $enProgress?->contract_collected_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="contract_collected_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="contract_collected_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="電気解約"
                            data-popup-date="{{ $enProgress?->electricity_cancellation_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="electricity_cancellation_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="electricity_cancellation_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="鍵渡し"
                            data-popup-date="{{ $enProgress?->key_handover_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="key_handover_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="key_handover_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="書類格納"
                            data-popup-date="{{ $enProgress?->documents_archived_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="documents_archived_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="documents_archived_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完了報告"
                            data-popup-date="{{ $enProgress?->completion_reported_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="completion_reported_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="completion_reported_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完了日"
                            data-popup-date="{{ $enProgress?->completed_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $enProgress->id }}"
                            data-field="completed_date"
                        >
                            <x-admin.progress.date :progress="$enProgress" field="completed_date" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div
        x-show="open"
        x-transition:enter="tw:transition tw:ease-out tw:duration-100"
        x-transition:enter-start="tw:opacity-0 tw:scale-95"
        x-transition:enter-end="tw:opacity-100 tw:scale-100"
        x-transition:leave="tw:transition tw:ease-in tw:duration-75"
        x-transition:leave-start="tw:opacity-100 tw:scale-100"
        x-transition:leave-end="tw:opacity-0 tw:scale-95"
        x-on:click.stop
        x-ref="popup"
        class="tw:fixed tw:z-50 tw:max-w-[320px] tw:rounded tw:border tw:border-gray-200 tw:bg-white tw:shadow-lg tw:p-2"
        :style="popupStyle"
        x-cloak
    >
        <div class="tw:text-sm tw:font-semibold tw:text-gray-800 tw:mb-2" x-text="popupTitle"></div>
        <x-form.calendar name="popup_date" />
    </div>
    <x-admin.sort-filter-dialog.text
        x-show="isTextFilter()"
        x-ref="filterPopupText"
        x-bind:style="popupStyleForFilter('text')"
        x-title="filterTitle"
        placeholder="IDで絞り込み"
        filter-model="filterValue"
        blank-model="filterBlank"
        sort-model="sortOrderDraft"
    />
    <x-admin.sort-filter-dialog.select
        x-show="isSelectFilter()"
        x-ref="filterPopupSelect"
        x-bind:style="popupStyleForFilter('select')"
        x-title="filterTitle"
        placeholder="担当者を選択"
        :options="$genpukuResponsibleOptions ?? []"
        select-name="select_filter"
        :select-value="''"
        :select-empty="true"
        filter-model="filterValue"
        blank-model="filterBlank"
        sort-model="sortOrderDraft"
    />
    <x-admin.sort-filter-dialog.date-range
        x-show="isDateRangeFilter()"
        x-ref="filterPopupDateRange"
        x-bind:style="popupStyleForFilter('date-range')"
        x-title="filterTitle"
        filter-model="filterValue"
        blank-model="filterBlank"
        sort-model="sortOrderDraft"
    />
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                const usePopup = (calendarName = 'popup_date') => ({
                    open: false,
                    popupTitle: '',
                    popupStyle: '',
                    activeTarget: null,
                    calendarName,

                    openPopup(trigger, event) {
                        if (trigger?.closest('[data-popup-disabled="1"]')) {
                            this.close();
                            return;
                        }
                        this.filterOpen = false;
                        this.activeTarget = trigger;
                        this.popupTitle = trigger.dataset.popupTitle ?? '';
                        this.open = true;
                        this.setPopupPosition(event, 'popup', 320, 'popupStyle');
                        this.setCalendarFromTarget(trigger);
                    },

                    handleDateInput(event) {
                        if (!this.open) {
                            return;
                        }
                        const input = event.target;
                        if (!input || !input.matches('[data-calendar-input]')) {
                            return;
                        }
                        this.applyDateToTarget(input.value ?? '');
                    },

                    handleCalendarInput(event) {
                        if (!this.open) {
                            return;
                        }
                        const detail = event?.detail ?? {};
                        if (detail.name && detail.name !== this.calendarName) {
                            return;
                        }
                        const progressId = this.activeTarget?.dataset?.progressId ?? '';
                        const field = this.activeTarget?.dataset?.field ?? '';
                        const normalized = this.normalizeDate(detail.value ?? '');
                        this.applyDateToTarget(normalized);
                        if (progressId && field && this.$wire?.updateDate) {
                            this.$wire.updateDate(progressId, field, normalized || null);
                        }
                        this.close();
                    },

                    setCalendarFromTarget(trigger) {
                        const raw = trigger.dataset.popupDate ?? trigger.textContent ?? '';
                        const value = this.normalizeDate(raw);
                        window.dispatchEvent(new CustomEvent('calendar-set', {
                            detail: { name: this.calendarName, value, silent: true },
                        }));
                    },

                    applyDateToTarget(value) {
                        if (!this.activeTarget) {
                            return;
                        }
                        if (value === 'ー') {
                            this.activeTarget.dataset.popupDate = 'ー';
                            this.activeTarget.textContent = 'ー';
                            return;
                        }
                        const formatted = this.formatMonthDay(value);
                        this.activeTarget.dataset.popupDate = formatted ? value : '';
                        this.activeTarget.textContent = formatted;
                    },

                    close() {
                        this.open = false;
                        this.activeTarget = null;
                    },

                    normalizeDate(value) {
                        const raw = String(value ?? '').trim();
                        if (!raw) {
                            return '';
                        }
                        if (raw === 'ー') {
                            return 'ー';
                        }
                        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                            return raw;
                        }
                        const fullDate = raw.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
                        if (fullDate) {
                            const year = fullDate[1];
                            const month = String(fullDate[2]).padStart(2, '0');
                            const day = String(fullDate[3]).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        }
                        const match = raw.match(/^(\d{1,2})[\/\-](\d{1,2})$/);
                        if (match) {
                            const year = new Date().getFullYear();
                            const month = String(match[1]).padStart(2, '0');
                            const day = String(match[2]).padStart(2, '0');
                            return `${year}-${month}-${day}`;
                        }
                        return '';
                    },

                    formatMonthDay(value) {
                        const raw = String(value ?? '').trim();
                        const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                        if (!match) {
                            return '';
                        }
                        return `${match[2]}/${match[3]}`;
                    },
                });

                const useFilter = ({
                    initialSortOrder = 'asc',
                    initialSortField = 'id',
                    initialFilters = {},
                } = {}) => ({
                    filterOpen: false,
                    filterPopupStyle: '',
                    filterTitle: '',
                    filterType: 'text',
                    filterSelectName: '',
                    sortOrder: initialSortOrder ?? 'asc',
                    sortField: initialSortField ?? 'id',
                    sortOrderDraft: initialSortOrder ?? 'asc',
                    sortFieldDraft: initialSortField ?? 'id',
                    filters: Array.isArray(initialFilters) ? {} : (initialFilters ?? {}),
                    filterField: 'id',
                    filterValue: '',
                    filterBlank: '',

                    openFilterPopup(trigger, event) {
                        this.close();
                        this.filterTitle = trigger.dataset.filterTitle ?? '';
                        const nextFilterField = trigger.dataset.filterField ?? this.filterField ?? 'id';
                        this.filterField = nextFilterField;
                        const currentFilter = this.filters[this.filterField] ?? {};
                        const currentValue = currentFilter.value ?? '';
                        this.filterValue = currentValue;
                        this.filterBlank = currentFilter.blank ?? '';
                        const nextSortField = trigger.dataset.sortField;
                        const hasSortField = !!nextSortField;
                        this.sortFieldDraft = hasSortField ? nextSortField : '';
                        this.sortOrderDraft = hasSortField && this.sortFieldDraft === this.sortField ? this.sortOrder : '';

                        const nextFilterType = trigger.dataset.filterType;
                        if (nextFilterType === 'select') {
                            this.filterType = 'select';
                        } else if (nextFilterType === 'date-range') {
                            this.filterType = 'date-range';
                        } else {
                            this.filterType = 'text';
                        }
                        if (this.filterType === 'date-range') {
                            if (!currentValue || typeof currentValue !== 'object') {
                                this.filterValue = { from: '', to: '' };
                            }
                        }

                        this.filterSelectName = trigger.dataset.filterSelectName ?? '';
                        if (this.filterType !== 'select') {
                            this.filterSelectName = '';
                        }
                        const filterOptionsRaw = trigger.dataset.filterOptions ?? '';
                        if (this.filterType === 'select' && this.filterSelectName && filterOptionsRaw) {
                            try {
                                const options = JSON.parse(filterOptionsRaw);
                                window.dispatchEvent(new CustomEvent('select-search-options', {
                                    detail: { name: this.filterSelectName, options, value: this.filterValue },
                                }));
                            } catch (error) {
                                console.warn('Invalid filter options JSON', error);
                            }
                        }
                        this.filterOpen = true;
                        const popupRef = this.filterType === 'select'
                            ? 'filterPopupSelect'
                            : (this.filterType === 'date-range' ? 'filterPopupDateRange' : 'filterPopupText');
                        this.setPopupPosition(event, popupRef, 260, 'filterPopupStyle');
                    },

                    closeFilter() {
                        this.filterOpen = false;
                    },

                    handleFilterInput(event) {
                        const value = event?.target?.value ?? '';
                        if (this.isFilterValueFilled()) {
                            if (this.filterBlank !== 'not_blank') {
                                this.filterBlank = 'not_blank';
                            }
                            return;
                        }
                        if (String(value).trim() !== '' && this.filterBlank !== 'not_blank') {
                            this.filterBlank = 'not_blank';
                        }
                    },

                    handleFilterBlankChange(event) {
                        const value = event?.target?.value ?? '';
                        if (value === 'blank') {
                            if (this.filterType === 'date-range' && this.filterValue && typeof this.filterValue === 'object') {
                                this.filterValue = { from: '', to: '' };
                            } else {
                                this.filterValue = '';
                            }
                        }
                    },

                    isFilterValueFilled() {
                        if (this.filterType === 'date-range') {
                            const from = this.filterValue?.from ?? '';
                            const to = this.filterValue?.to ?? '';
                            return String(from).trim() !== '' || String(to).trim() !== '';
                        }
                        return String(this.filterValue ?? '').trim() !== '';
                    },

                    applySortFilter() {
                        if (this.$wire?.updateSortFilter) {
                            const nextSortOrder = this.sortOrderDraft || this.sortOrder;
                            const nextSortField = this.sortOrderDraft ? this.sortFieldDraft : this.sortField;
                            this.sortOrder = nextSortOrder;
                            this.sortField = nextSortField;
                            if (!this.isFilterValueFilled() && this.filterBlank === '') {
                                const nextFilters = { ...(this.filters ?? {}) };
                                delete nextFilters[this.filterField];
                                this.filters = nextFilters;
                            } else {
                                this.filters = {
                                    ...(this.filters ?? {}),
                                    [this.filterField]: {
                                        value: this.filterValue,
                                        blank: this.filterBlank,
                                    },
                                };
                            }
                            this.$wire.updateSortFilter(nextSortOrder, nextSortField, this.filters);
                        }
                        this.closeFilter();
                    },

                    resetSortFilter() {
                        if (this.filterType === 'date-range') {
                            this.filterValue = { from: '', to: '' };
                        } else {
                            this.filterValue = '';
                        }
                        this.filterBlank = '';
                        const nextFilters = { ...(this.filters ?? {}) };
                        delete nextFilters[this.filterField];
                        this.filters = nextFilters;
                        if (this.filterType === 'select' && this.filterSelectName) {
                            window.dispatchEvent(new CustomEvent('select-search-clear', {
                                detail: { name: this.filterSelectName },
                            }));
                        }
                        if (this.$wire?.updateSortFilter) {
                            this.$wire.updateSortFilter(this.sortOrder, this.sortField, this.filters);
                        }
                        this.closeFilter();
                    },

                    isTextFilter() {
                        return this.filterOpen && this.filterType === 'text';
                    },

                    isSelectFilter() {
                        return this.filterOpen && this.filterType === 'select';
                    },

                    isDateRangeFilter() {
                        return this.filterOpen && this.filterType === 'date-range';
                    },

                    popupStyleForFilter(type) {
                        return this.filterOpen && this.filterType === type ? this.filterPopupStyle : 'display: none;';
                    },

                    clearAllFilters() {
                        this.sortOrder = 'asc';
                        this.sortField = 'id';
                        this.sortOrderDraft = 'asc';
                        this.sortFieldDraft = 'id';
                        this.filters = {};
                        this.filterField = 'id';
                        this.filterValue = '';
                        this.filterBlank = '';
                        if (this.$wire?.updateSortFilter) {
                            this.$wire.updateSortFilter(this.sortOrder, this.sortField, this.filters);
                        }
                        this.closeFilter();
                    },
                });

                Alpine.data('tablePopup', (initialSortOrder = 'asc', initialSortField = 'id', initialFilters = {}) => ({
                    ...usePopup('popup_date'),
                    ...useFilter({
                        initialSortOrder,
                        initialSortField,
                        initialFilters,
                    }),

                    handleClick(event) {
                        const filterTrigger = event.target.closest('[data-filter-trigger]');
                        if (filterTrigger) {
                            this.openFilterPopup(filterTrigger, event);
                            return;
                        }
                        const trigger = event.target.closest('[data-popup-title]');
                        if (trigger) {
                            this.openPopup(trigger, event);
                            return;
                        }
                        this.closeAll();
                    },

                    closeAll() {
                        this.close();
                        this.closeFilter();
                    },

                    setPopupPosition(event, refName, fallbackWidth, styleKey) {
                        const x = event.clientX + 12;
                        const y = event.clientY + 12;
                        this.$nextTick(() => {
                            const popup = this.$refs[refName];
                            const rect = popup?.getBoundingClientRect();
                            const width = rect?.width ?? fallbackWidth;
                            const height = rect?.height ?? 0;
                            const margin = 8;
                            let left = x;
                            let top = y;
                            const maxLeft = window.innerWidth - width - margin;
                            if (left > maxLeft) {
                                left = Math.max(margin, maxLeft);
                            }
                            const maxTop = window.innerHeight - height - margin;
                            if (top > maxTop) {
                                top = Math.max(margin, maxTop);
                            }
                            this[styleKey] = `left: ${left}px; top: ${top}px;`;
                        });
                    },
                }));
            });
        </script>
    @endpush
@endonce
