<div>
    <div class="tw:h-[42px] tw:flex tw:items-center tw:justify-end tw:gap-x-[26px]">
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">マイソク</x-button.black>
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">新規募集条件</x-button.black>
        <x-button.black class="tw:!h-[28px] tw:!px-[15px] tw:!rounded-lg tw:!w-[150px]">WP審査基準</x-button.black>
    </div>
    <div class="tw:h-[21px] tw:text-[1.2rem] tw:font-bold">
        契約条件（申込ID：{{ $enProgress->application_id }}）
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc]">完工予定日</div>
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">完工日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0">契約形態</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-left tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-l-0 tw:pl-3">
            <x-form.checkbox name="fr_active_flag" class="tw:!text-[#ff0000] tw:accent-[#ff0000]" label="" label_class="tw:!text-[#ff0000]">FR期間</x-form.checkbox>
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[182px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            {{ $latestGeProgress?->completion_scheduled_date?->format('Y年m月d日') }}
        </div>
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            {{ $latestGeProgress?->completion_received_date?->format('Y年m月d日') }}
        </div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            {{ $enProgress->contract_type }}
        </div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-left tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:pl-3 tw:text-[#ff0000]">
            <x-form.input-date name="fr_start_date" class="tw:!h-[19px] tw:!w-[90px] tw:text-[#ff0000]" />
            〜
            <x-form.input-date name="fr_end_date" class="tw:!h-[19px] tw:!w-[90px] tw:text-[#ff0000]" />
            (2ヶ月)
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">契約希望日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">入金予定日</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">入居希望日</div>
        <div class="tw:w-[286px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">契約期間</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">更新料</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input-date name="desired_contract_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-l-0 tw:border-t-0">
            <x-form.input-date name="planned_payment_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-date name="desired_move_in_date" class="tw:!h-[40px] tw:!text-center" />
        </div>
        <div class="tw:w-[286px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.input-date name="fr_start_date" class="tw:!h-[40px] tw:!w-[90px]" />
            〜
            <x-form.input-date name="fr_end_date" class="tw:!h-[40px] tw:!w-[90px]" />
            (2ヶ月)
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input name="renewal_fee" class="tw:!h-[40px] tw:text-center tw:border-0" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[208px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">保証会社名</div>
        <div class="tw:w-[312px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">プラン名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">月額費用</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">法人除外</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">連帯保証</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[208px] tw:h-[42px] tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.select name="guarantee_company_id" :options="$guaranteeCompanyOptions" class="tw:!w-[206px] tw:!h-[40px]" />
        </div>
        <div class="tw:w-[312px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="guarantor_plan_name" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="guarantor_monthly_fee" class="tw:!h-[40px]" :border="false" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="guarantee_company_status" :value="App\Models\EnProgress::GUARANTEE_COMPANY_STATUS_CORPORATE_EXEMPT" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="guarantee_company_status" :value="App\Models\EnProgress::GUARANTEE_COMPANY_STATUS_JOINT_GUARANTOR" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[520px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">火災保険名</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">月額費用</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">法人除外</div>
        <div class="tw:w-[78px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">個人加入</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[520px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0">
            <x-form.input name="fire_insurance_name" class="tw:!h-[40px] tw:border-0" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">
            <x-form.input-number name="fire_insurance_monthly_fee" class="tw:!h-[40px] tw:border-0" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="fire_insurance_status" :value="App\Models\EnProgress::FIRE_INSURANCE_STATUS_CORPORATE_EXEMPT" />
        </div>
        <div class="tw:w-[78px] tw:h-[42px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.radio name="fire_insurance_status" :value="App\Models\EnProgress::FIRE_INSURANCE_STATUS_INDIVIDUAL" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">安心入居</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">退去時清掃徴収</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ACクリーニング</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">解約違約金</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">ペット</div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">楽器</div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:leading-[40px]">
            <x-form.select2 name="anshin_support_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="move_out_cleaning_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="ac_cleaning_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="cancellation_penalty_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="pet_allowed_flag" :options="['0' => '不可', '1' => '可']" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="instrument_allowed_flag" :options="['0' => '不可', '1' => '可']" />
        </div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0">FR</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0">二人入居</div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[130px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
        <div class="tw:w-[156px] tw:h-[21px] tw:text-center tw:bg-[#f3f3f3] tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0"></div>
    </div>
    <div class="tw:flex">
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:leading-[40px]">
            <x-form.select2 name="fr_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="two_person_allowed_flag" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[130px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
        <div class="tw:w-[156px] tw:h-[42px] tw:px-[10px] tw:text-center tw:border tw:border-[#cccccc] tw:border-t-0 tw:border-l-0 tw:leading-[40px]">
            <x-form.select2 name="" :options="['0' => 'ー', '1' => '◯']" />
        </div>
    </div>
</div>
