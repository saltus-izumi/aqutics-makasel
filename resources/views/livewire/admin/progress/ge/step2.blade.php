<div class="tw:w-[806px]">
    <div class="tw:w-full tw:pl-1 tw:bg-[#f3f3f3]">
        STEP２（退去立会報告）
    </div>
    <div class="tw:w-full tw:px-[26px]">
        <div class="tw:h-[47px] tw:flex tw:gap-x-[26px] tw:items-center">
            <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">原復価格規定</x-button.black>
            <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">精算ルール</x-button.black>
        </div>
        <table class="tw:w-full tw:table-fixed">
            <tr class="tw:h-[42px]">
                <td class="tw:w-[130px] tw:text-center tw:bg-black tw:text-[1.3rem] tw:font-bold tw:text-white tw:border tw:border-[#cccccc]">返金額</td>
                <td class="tw:border tw:border-[#cccccc]"></td>
                <td class="tw:w-[130px] tw:text-center tw:bg-black tw:text-[1.3rem] tw:font-bold tw:text-white tw:border tw:border-[#cccccc]">振込期日</td>
                <td class="tw:border tw:border-[#cccccc]"></td>
            </tr>
        </table>
        <div class="tw:mt-[21px]">
            <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">原復会社様入力</div>
            <table class="tw:w-full tw:table-fixed tw:mt-[px]">
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">小計A</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">敷金預託等</td>
                    <td class="tw:border tw:border-[#cccccc]">
                        {{ number_format($progress->geProgress?->security_deposit_amount)}}
                    </td>
                </tr>
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">小計B</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">日割り家賃</td>
                    <td class="tw:border tw:border-[#cccccc]">
                        {{ number_format($progress->geProgress?->prorated_rent_amount) }}
                    </td>
                </tr>
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">小計C</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">違約金（償却等）</td>
                    <td class="tw:border tw:border-[#cccccc]">
                        {{ number_format($progress->geProgress?->penalty_forfeiture_amount) }}
                    </td>
                </tr>
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">工事負担額（税抜）</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:border tw:border-[#cccccc]">その他</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                </tr>
                <tr class="tw:h-[42px]">
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">工事負担額（税込）</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                    <td class="tw:text-[1.2rem] tw:text-center tw:bg-[#efefef] tw:border tw:border-[#cccccc]">精算額</td>
                    <td class="tw:border tw:border-[#cccccc]"></td>
                </tr>
            </table>
        </div>

        <div class="tw:mt-[21px]">
            <div class="tw:h-[21px] tw:text-[0.9rem] tw:text-[#999999]">
                添付ファイル・画像、ＰＤＦ、Excel、Wordファイルが送信可能です。（可能ファイル数：20個／1ファイルの最大サイズ：25MB）
            </div>
            <div class="tw:flex tw:gap-x-[26px]">
                <div class="tw:flex-1">
                    <div class="tw:w-full tw:h-[42px] tw:bg-[#cfe2f3]"></div>
                </div>
                <div class="tw:flex-1">
                    <div class="tw:w-full tw:h-[42px] tw:bg-[#cfe2f3]"></div>
                </div>
                <div class="tw:flex-1">
                    <div class="tw:w-full tw:h-[42px] tw:bg-[#cfe2f3]"></div>
                </div>
            </div>
        </div>
        <div class="tw:mt-[21px]">
            立会完了メッセージ<br>
            <x-form.textarea class="tw:!h-[105px]" placeholder="引継ぎコメント"></x-form.textarea>
        </div>
        <div class="tw:h-[42px] tw:mt-[26px] tw:flex tw:justify-end tw:items-center tw:gap-x-[26px]">
            <div>
                <x-button.blue class="tw:!h-[31px] tw:!rounded-lg tw:text-[1.2rem]">立会完了送信</x-button.blue>
            </div>
        </div>
    </div>
</div>
