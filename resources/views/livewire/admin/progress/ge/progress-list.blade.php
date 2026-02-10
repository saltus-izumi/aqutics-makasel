<div
    class="tw:relative"
    x-data="tablePopup(@js($sortOrder ?? 'asc'), @js($sortField ?? 'id'), @js($filters ?? []))"
    x-on:click="handleClick($event)"
    x-on:input="handleDateInput($event)"
    x-on:calendar-input.window="handleCalendarInput($event)"
    x-on:keydown.escape.window="closeAll()"
>
    <table class="tw:table-fixed tw:w-[1352px] tw:min-w-[1352px]">
        <colgroup>
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[182px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[78px]">
            <col class="tw:w-[78px]">
            <col class="tw:w-[182px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
            <col class="tw:w-[52px]">
        </colgroup>
        <thead class="tw:sticky tw:top-0 tw:z-10">
            <tr class="tw:h-[21px] tw:bg-white">
                <td rowspan="2" colspan="2">
                    <x-button.blue class="tw:!h-[21px] tw:!w-[104px] tw:!font-normal">検索</x-button.blue>
                </td>
                <td class="tw:pl-[10px]" rowspan="2">
                    案件数  {{ $progresses->count() }}
                    <button type="button" class="tw:ml-2 tw:text-xs tw:px-2 tw:py-0.5 tw:border tw:rounded tw:cursor-pointer" x-on:click="clearAllFilters()">フィルタークリア</button>
                </td>
                <td rowspan="2" colspan="4"></td>
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
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center" colspan="2">KPI_LT</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">2日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">5日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">0日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">7日</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
                <td class="tw:text-[#0000ff] tw:bg-[#c9daf8] tw:text-center">ー</td>
            </tr>
            <tr class="tw:h-[50px]">
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">原復ID</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">物件ID</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">物件名</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">号室</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">責任者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">実行者</td>
                <td class="tw:text-center tw:bg-[#efefef]" rowspan="2">ネクストアクション</td>
                <td class="tw:text-center tw:bg-black tw:text-white tw:leading-[1.1rem]">退去<br>受付</td>
                <td class="tw:text-center tw:bg-[#efefef]">解約日</td>
                <td class="tw:text-center tw:bg-[#efefef]">退去日</td>
                <td class="tw:text-center tw:bg-[#efefef]">下代</td>
                <td class="tw:text-center tw:bg-[#efefef]">通電</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">借主<br>負担</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">貸主<br>提案</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">貸主<br>承諾</td>
                <td class="tw:text-center tw:bg-[#efefef]">発注</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>予定</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>受信</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">完工<br>報告</td>
                <td class="tw:text-center tw:bg-[#efefef] tw:leading-[1.1rem]">革命<br>控除</td>
                <td class="tw:text-center tw:bg-black tw:text-white">完了</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center" colspan="2">実質LT</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['taikyo'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['genpuku_mitsumori_recieved'] ?? 'ー' }}</td>
                <td class="tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['tsuden'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['tenant_charge_confirmed'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['genpuku_teian'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['genpuku_teian_kyodaku'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['genpuku_kouji_hachu'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['kanko_yotei'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['kanko_jyushin_date'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['owner_kanko_houkoku'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['kakumei_koujo_touroku'] ?? 'ー' }}</td>
                <td class="tw:text-[#ff0000] tw:bg-[#c9daf8] tw:text-center">{{ $averageLt['ge_complete'] ?? 'ー' }}</td>
            </tr>
            <tr class="tw:h-[21px]">
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="原復ID"
                        data-sort-field="id"
                        data-filter-field="id"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="物件ID"
                        data-sort-field="investment_id"
                        data-filter-field="investment_id"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('investment_id')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="物件名"
                        data-sort-field="investment_name"
                        data-filter-field="investment_name"
                        data-filter-type="text"
                        @class(['tw:text-red-600' => $this->hasFilter('investment_name')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
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
                        data-filter-title="責任者"
                        data-sort-field="genpuku_responsible_id"
                        data-filter-field="genpuku_responsible_id"
                        data-filter-type="select"
                        data-filter-select-name="select_filter"
                        data-filter-options='@json($genpukuResponsibleOptions ?? [])'
                        @class(['tw:text-red-600' => $this->hasFilter('genpuku_responsible_id')])
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
                        data-filter-title="退去受付"
                        data-sort-field="taikyo_uketuke_date"
                        data-filter-field="taikyo_uketuke_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('taikyo_uketuke_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="解約日"
                        data-sort-field="cancellation_date"
                        data-filter-field="cancellation_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('cancellation_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="退去日"
                        data-sort-field="taikyo_date"
                        data-filter-field="taikyo_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('taikyo_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="下代"
                        data-sort-field="genpuku_mitsumori_recieved_date"
                        data-filter-field="genpuku_mitsumori_recieved_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('genpuku_mitsumori_recieved_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="通電"
                        data-sort-field="tsuden"
                        data-filter-field="tsuden"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('tsuden')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="借主負担"
                        data-sort-field="tenant_charge_confirmed_date"
                        data-filter-field="tenant_charge_confirmed_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('tenant_charge_confirmed_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="貸主提案"
                        data-sort-field="genpuku_teian_date"
                        data-filter-field="genpuku_teian_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('genpuku_teian_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="貸主承諾"
                        data-sort-field="genpuku_teian_kyodaku_date"
                        data-filter-field="genpuku_teian_kyodaku_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('genpuku_teian_kyodaku_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="発注"
                        data-sort-field="genpuku_kouji_hachu_date"
                        data-filter-field="genpuku_kouji_hachu_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('genpuku_kouji_hachu_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完工予定"
                        data-sort-field="kanko_yotei_date"
                        data-filter-field="kanko_yotei_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('kanko_yotei_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完工受信"
                        data-sort-field="kanko_jyushin_date"
                        data-filter-field="kanko_jyushin_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('kanko_jyushin_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完工報告"
                        data-sort-field="owner_kanko_houkoku_date"
                        data-filter-field="owner_kanko_houkoku_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('owner_kanko_houkoku_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="革命控除"
                        data-sort-field="kakumei_koujo_touroku_date"
                        data-filter-field="kakumei_koujo_touroku_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('kakumei_koujo_touroku_date')])
                    >▼</div>
                </td>
                <td class="tw:bg-[#cccccc] tw:text-center tw:text-[0.6rem] tw:cursor-pointer">
                    <div
                        data-filter-trigger
                        data-filter-title="完了"
                        data-sort-field="ge_complete_date"
                        data-filter-field="ge_complete_date"
                        data-filter-type="date-range"
                        @class(['tw:text-red-600' => $this->hasFilter('ge_complete_date')])
                    >▼</div>
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach ($progresses as $progress)
                <tr class="tw:h-[42px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">
                        <a href="{{ route('admin.progress.ge.detail', ['progressId' => $progress->id]) }}" class="tw:text-pm_blue_001">{{ $progress->id }}</a>
                    </td>
                    <td class="tw:text-center">{{ $progress->investment_id }}</td>
                    <td>{{ $progress?->investment?->investment_name }}</td>
                    <td class="tw:text-center">{{ $progress?->investment_room_uid == 0 ? '共用部' : $progress?->investmentRoom?->investment_room_number }}</td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-form.select
                            name="genpuku_responsible_id"
                            :options="$genpukuResponsibleShortOptions"
                            empty="　"
                            :value="$progress->genpuku_responsible_id"
                            wire:input="updateSelectValue({{ $progress->id }}, 'genpuku_responsible_id', $event.target.value)"
                        />
                    </td>
                    <td class="tw:text-center tw:px-[3px]">
                        <x-form.select
                            name="executor_user_id"
                            :options="$genpukuResponsibleShortOptions"
                            empty="　"
                            :value="$progress->geProgress->executor_user_id"
                            wire:input="updateSelectValue({{ $progress->id }}, 'executor_user_id', $event.target.value)"
                        />
                    </td>
                    <td class="tw:text-center">{{ App\Models\GeProgress::NEXT_ACTIONS[$progress?->geProgress?->next_action] ?? '' }}</td>
                    <td class="tw:text-center">
                        <x-tooltip :text="$progress?->taikyo_uketuke_date?->format('Y/m/d')">
                            {{ $progress?->taikyo_uketuke_date?->format('m/d') }}
                        </x-tooltip>
                    </td>
                    <td class="tw:text-center tw:overflow-visible">
                        <x-tooltip :text="$progress?->investmentEmptyRoom?->cancellation_date?->format('Y/m/d')">
                            {{ $progress?->investmentEmptyRoom?->cancellation_date?->format('m/d') }}
                        </x-tooltip>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="退去日"
                            data-popup-date="{{ $progress?->taikyo_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="taikyo_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="taikyo_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        @php
                            $costEstimateFileList = collect($progress?->geProgress?->costEstimateFiles ?? [])
                                ->filter(fn($file) => !empty($file?->id))
                                ->mapWithKeys(fn($file) => [route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]) => $file->file_name ?? ''])
                                ->all();
                            $costEstimateFileCount = count($costEstimateFileList);
                        @endphp
                        <div class="tw:flex tw:flex-col tw:items-center tw:gap-[4px]" x-data="{ fileListOpen: false }">
                            <div
                                class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                                @click="if ({{ $costEstimateFileCount }} > 0) { fileListOpen = true }"
                            >
                                <x-admin.progress.date :progress="$progress" field="genpuku_mitsumori_recieved_date" />
                            </div>
                            <template x-teleport="body">
                                <div
                                    x-cloak
                                    x-show="fileListOpen"
                                    x-transition.opacity
                                    class="tw:fixed tw:inset-0 tw:z-[300] tw:flex tw:items-center tw:justify-center tw:bg-black/40 tw:px-[16px]"
                                    role="dialog"
                                    aria-modal="true"
                                    @click.self="fileListOpen = false"
                                >
                                    <div
                                        x-show="fileListOpen"
                                        x-transition
                                        class="tw:w-full tw:max-w-[640px] tw:max-h-[80vh] tw:overflow-y-auto tw:rounded-[8px] tw:bg-white tw:shadow-lg"
                                    >
                                        <div class="tw:flex tw:items-center tw:justify-between tw:border-b tw:border-b-gray-200 tw:px-[16px] tw:py-[12px]">
                                            <div class="tw:text-[1.2rem] tw:font-bold">ファイル一覧</div>
                                            <button
                                                type="button"
                                                class="tw:text-[1.4rem] tw:text-gray-500"
                                                @click="fileListOpen = false"
                                                aria-label="閉じる"
                                            >
                                                ×
                                            </button>
                                        </div>
                                        <div class="tw:px-[16px] tw:py-[12px]">
                                            <x-file-list :files="$costEstimateFileList" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="通電"
                            data-popup-date="{{ $progress?->tsuden?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="tsuden"
                        >
                            <x-admin.progress.date :progress="$progress" field="tsuden" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="借主負担"
                            data-popup-date="{{ $progress?->tenant_charge_confirmed_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="tenant_charge_confirmed_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="tenant_charge_confirmed_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="貸主提案"
                            data-popup-date="{{ $progress?->genpuku_teian_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="genpuku_teian_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="genpuku_teian_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="貸主承諾"
                            data-popup-date="{{ $progress?->genpuku_teian_kyodaku_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="genpuku_teian_kyodaku_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="genpuku_teian_kyodaku_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="発注"
                            data-popup-date="{{ $progress?->genpuku_kouji_hachu_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="genpuku_kouji_hachu_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="genpuku_kouji_hachu_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完工予定"
                            data-popup-date="{{ $progress?->kanko_yotei_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="kanko_yotei_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="kanko_yotei_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完工受信"
                            data-popup-date="{{ $progress?->kanko_jyushin_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="kanko_jyushin_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="kanko_jyushin_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完工報告"
                            data-popup-date="{{ $progress?->owner_kanko_houkoku_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="owner_kanko_houkoku_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="owner_kanko_houkoku_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="革命控除"
                            data-popup-date="{{ $progress?->kakumei_koujo_touroku_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="kakumei_koujo_touroku_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="kakumei_koujo_touroku_date" />
                        </div>
                    </td>
                    <td class="tw:text-center">
                        <div class="tw:inline-flex tw:w-full tw:h-full tw:items-center tw:justify-center tw:cursor-pointer"
                            data-popup-title="完了"
                            data-popup-date="{{ $progress?->ge_complete_date?->format('Y/m/d') }}"
                            data-progress-id="{{ $progress->id }}"
                            data-field="ge_complete_date"
                        >
                            <x-admin.progress.date :progress="$progress" field="ge_complete_date" />
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
