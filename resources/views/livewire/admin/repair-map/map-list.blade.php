<div class="tw:h-full tw:w-full tw:overflow-auto tw:bg-white">
    <div class="tw:h-[367px] tw:w-full tw:sticky tw:top-0 tw:bg-white tw:z-[40]">
        <div class="tw:h-[80px] tw:sticky tw:top-0 tw:bg-white tw:z-[40]">
            <div class="tw:font-bold tw:text-[1.3rem]">物件選択</div>
            <x-form.input class="tw:!w-[220px]" placeholder="物件名（ID）、オーナー名" />
        </div>
        <div class="tw:h-[120px] tw:sticky tw:top-[80px] tw:bg-white tw:z-[30]">
            <div class="tw:font-bold tw:text-[1.3rem] tw:!w-[840px] tw:border-b">専有部</div>
            <div class="tw:mt-[20px] tw:!pl-[20px] tw:flex tw:gap-x-[40px]">
                <div class="tw:!w-[220px]">
                    PC｜大カテゴリ<br>
                    <x-form.select-search
                        name="pc_category1_master_id"
                        wire:model.live="selectedPcCategory1MasterId"
                        :options="$pcCategory1MasterOptions"
                        :value="$selectedPcCategory1MasterId"
                        empty=" "
                    />
                </div>
                <div class="tw:!w-[220px]">
                    PC｜中カテゴリ<br>
                    <x-form.select-search
                        name="pc_category2_master_id"
                        wire:model.live="selectedPcCategory2MasterId"
                        :options="$pcCategory2MasterOptions"
                        :value="$selectedPcCategory2MasterId"
                        empty=" "
                    />
                </div>
                <div class="tw:!w-[220px]">
                    PC｜小カテゴリ<br>
                    <x-form.select-search
                        name="pc_category3_master_id"
                        wire:model.live="selectedPcCategory3MasterId"
                        :options="$pcCategory3MasterOptions"
                        :value="$selectedPcCategory3MasterId"
                        empty=" "
                    />
                </div>
            </div>
        </div>
        <div class="tw:h-[120px] tw:sticky tw:top-[200px] tw:bg-white tw:z-[20]">
            <div class="tw:font-bold tw:text-[1.3rem] tw:!w-[840px] tw:border-b">共用部</div>
            <div class="tw:mt-[20px] tw:!pl-[20px] tw:flex tw:gap-x-[40px]">
                <div class="tw:!w-[220px]">
                    共用｜中カテゴリ<br>
                    <x-form.select-search
                        name="common_category1_master_id"
                        wire:model.live="selectedCommonCategory1MasterId"
                        :options="$commonCategory1MasterOptions"
                        :value="$selectedCommonCategory1MasterId"
                        empty=" "
                    />
                </div>
                <div class="tw:!w-[220px]">
                    共用｜小カテゴリ<br>
                    <x-form.select-search
                        name="common_category2_master_id"
                        wire:model.live="selectedCommonCategory2MasterId"
                        :options="$commonCategory2MasterOptions"
                        :value="$selectedCommonCategory2MasterId"
                        empty=" "
                    />
                </div>
            </div>
        </div>
        <div class="tw:sticky tw:top-[320px] tw:font-bold tw:text-[1.3rem] tw:!w-[840px] tw:bg-white tw:border-b tw:z-[15]">取引先</div>
        <div class="tw:h-[20px] tw:full tw:sticky tw:top-[347px] tw:bg-white tw:z-[15]"> </div>
    </div>
    <div class="tw:!pl-[20px] tw:bg-white">
        <table class="tw:table-fixed tw:w-[1060px] tw:min-w-[1060px]">
            <colgroup>
                <col class="tw:w-[40px]">       {{-- ID --}}
                <col class="tw:w-[180px]">      {{-- 社名 --}}
                <col class="tw:w-[140px]">      {{-- 対応｜中カテゴリ --}}
                <col class="tw:w-[140px]">      {{-- 対応｜小カテゴリ --}}
                <col class="tw:w-[140px]">      {{-- 電話 --}}
                <col class="tw:w-[140px]">      {{-- メール --}}
                <col class="tw:w-[280px]">      {{-- 所在 --}}
            </colgroup>
            <thead class="tw:sticky tw:top-[367px] tw:z-10">
                <tr class="tw:h-[40px]">
                    <td class="tw:text-center tw:bg-[#efefef]">ID</td>
                    <td class="tw:bg-[#efefef]">社名</td>
                    <td class="tw:bg-[#efefef]">対応｜中カテゴリ</td>
                    <td class="tw:bg-[#efefef]">対応｜小カテゴ</td>
                    <td class="tw:bg-[#efefef]">電話</td>
                    <td class="tw:bg-[#efefef]">メール</td>
                    <td class="tw:bg-[#efefef]">所在</td>
                </tr>
            </thead>
            <tbody>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
                <tr class="tw:h-[25px] tw:border-b tw:border-b-[#cccccc]">
                    <td class="tw:text-center">ID</td>
                    <td>社名</td>
                    <td>対応｜中カテゴリ</td>
                    <td>対応｜小カテゴ</td>
                    <td>電話</td>
                    <td>メール</td>
                    <td>所在</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
