<?php

namespace App\Livewire\Admin\Import;

use App\Models\Broker;
use App\Models\BrokerInvestment;
use App\Models\CorporateTenancyApplicationLog;
use App\Models\EnProgress;
use App\Models\EnProgressEmergencyContact;
use App\Models\EnProgressGuarantor;
use App\Models\EnProgressCorporateApplicant;
use App\Models\EnProgressOccupants;
use App\Models\GuaranteeCompany;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\Progress;
use App\Models\ReactionCompany;
use App\Models\SummaryPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class CorporateTenancyApplicationImport extends Component
{
    use WithFileUploads;

    public $corporateTenancyApplicationFile = null;

    public ?int $readCount = null;
    public ?int $insertResidentCount = null;
    public ?int $updateResidentCount = null;
    public ?int $errorCount = null;
    public array $errorMessages = [];

    protected $messages = [
        'corporateTenancyApplicationFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        $this->validate([
            'corporateTenancyApplicationFile' => ['required', 'file'],
        ]);

        $this->readCount = 0;
        $this->insertResidentCount = 0;
        $this->updateResidentCount = 0;
        $this->errorCount = 0;
        $this->errorMessages = [];

        $originalName = $this->corporateTenancyApplicationFile->getClientOriginalName();
        if (!preg_match('/^法人申込-([0-9]{8})\.csv$/u', $originalName, $matches)) {
            $this->errorCount = 1;
            $this->errorMessages[] = '不正なファイル名です。';
            return;
        }

        $startYear = (int) substr($matches[1], 0, 4);
        $startMonth = (int) substr($matches[1], 4, 2);
        $startDay = (int) substr($matches[1], 6, 2);
        if (!checkdate($startMonth, $startDay, $startYear)) {
            $this->errorCount = 1;
            $this->errorMessages[] = '不正なファイル名です。';
            return;
        }

        $samplingDate = Carbon::create($startYear, $startMonth, $startDay)->startOfDay();
        $startDate = $samplingDate->copy();
        $endDate = $startDate->copy()->addDays(6);

        DB::beginTransaction();

        try {
            SummaryPeriod::query()->firstOrCreate(
                ['start_date' => $startDate->toDateString()],
                ['end_date' => $endDate->toDateString()]
            );

            ReactionCompany::query()
                ->whereDate('sampling_date', $samplingDate->toDateString())
                ->delete();

            $csv = new \SplFileObject($this->corporateTenancyApplicationFile->getRealPath(), 'r');
            $rowNo = 0;
            while (!$csv->eof()) {
                $row = $csv->fgetcsv();
                if ($row === false || $row === [null]) {
                    continue;
                }

                $rowNo++;
                if ($rowNo === 1) {
                    continue;
                }

                $record = $this->convertEncoding($row);
                $this->insertLog($record);

                $regData = $this->mapCsvRow($record, 176);
                if ($regData === []) {
                    continue;
                }

                $this->readCount++;
                $regData['sampling_date'] = $samplingDate->toDateString();

                $investment = $this->findInvestment($regData['ru003'] ?? '');
                $room = $this->findRoom($investment, $regData['ru004'] ?? '');
                $regData['investment_id'] = 0;
                $regData['investment_room_id'] = 0;
                $regData['en_staff_id'] = null;

                if ($investment) {
                    $regData['investment_id'] = $investment->id;
                    $regData['en_staff_id'] = $investment->en_staff_id;
                }
                if ($room) {
                    $regData['investment_room_id'] = $room->investment_room_id;
                    $regData['investment_room_uid'] = $room->id;
                } elseif (($regData['ru001'] ?? null) === "\x1a") {
                    continue;
                } else {
                    $this->errorCount = ($this->errorCount ?? 0) + 1;
                    if (!$investment) {
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s に該当する物件情報がありません。',
                            $rowNo,
                            $regData['ru003'] ?? ''
                        );
                    } else {
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s %s に該当する部屋情報がありません。',
                            $rowNo,
                            $regData['ru003'] ?? '',
                            $regData['ru004'] ?? ''
                        );
                    }
                }

                ReactionCompany::query()->create($regData);

                // if (($regData['ru035'] ?? null) === '承認' && $room) {
                //     $this->upsertInvestmentRoomResident($room, $regData);
                // }

                $broker = $this->upsertBroker($regData);
                if ($room && $broker) {
                    BrokerInvestment::query()->firstOrCreate([
                        'broker_id' => $broker->id,
                        'investment_id' => $room->investment_id,
                    ]);
                }
                $regData['broker_id'] = $broker->id;

                $this->upsertEnProgress($regData);
            }

            if ($this->errorCount == 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->errorCount = ($this->errorCount ?? 0) + 1;
            $this->errorMessages[] = '取り込み処理中にエラーが発生しました。(' . $e . ')';
        }

        $this->reset('corporateTenancyApplicationFile');
    }

    /**
     * @param array<int, mixed> $row
     * @return array<int, mixed>
     */
    protected function convertEncoding(array $row): array
    {
        return array_map(
            static function ($value) {
                if (!is_string($value)) {
                    return $value;
                }
                return mb_convert_encoding($value, 'UTF-8', 'CP932');
            },
            $row
        );
    }

    /**
     * @param array<int, mixed> $row
     * @return array<string, mixed>
     */
    protected function mapCsvRow(array $row, int $maxIndex): array
    {
        $mapped = [];
        for ($i = 0; $i <= $maxIndex; $i++) {
            if (!array_key_exists($i, $row) || $row[$i] === '' || $row[$i] === null) {
                continue;
            }
            $mapped[sprintf('ru%03d', $i + 1)] = $row[$i];
        }
        return $mapped;
    }

    protected function findInvestment(string $investmentName): ?Investment
    {
        if ($investmentName === '') {
            return null;
        }

        return Investment::getByInvestmentNameForProcall($investmentName);
    }

    protected function findRoom(?Investment $investment, string $roomNumber): ?InvestmentRoom
    {
        if (!$investment || $roomNumber === '') {
            return null;
        }

        return InvestmentRoom::getByInvestmentRoomNumberForProcall($investment->id, $roomNumber);
    }

    /**
     * @param array<string, mixed> $regData
     */
    protected function upsertInvestmentRoomResident(InvestmentRoom $room, array $regData): void
    {
        $resident = InvestmentRoomResident::query()
            ->where(function ($query) use ($room) {
                $query
                    ->where('investment_id', $room->investment_id)
                    ->where('investment_room_id', $room->investment_room_id);
            })
            ->orWhere('investment_room_uid', $room->id)
            ->first();

        $payload = [
            'investment_id' => $room->investment_id,
            'investment_uid' => $room->id,
            'investment_room_id' => $room->investment_room_id,
            'investment_room_uid' => $room->id,
            'contractor_name' => ($regData['ru063'] ?? '') . '　' . ($regData['ru086'] ?? '') . '　' . ($regData['ru087'] ?? ''),
            'age' => $regData['ru098'] ?? null,
            'workplace' => $regData['ru063'] ?? null,
            'annual_income' => $regData['ru100'] ?? null,
        ];

        if (isset($regData['ru146'], $regData['ru147'])) {
            $payload['guarantor_name'] = $regData['ru146'] . '　' . $regData['ru147'];
        }

        if ($resident) {
            $this->updateResidentCount++;
            $resident->fill($payload);
            $resident->save();
            return;
        }

        InvestmentRoomResident::query()->create($payload);
        $this->insertResidentCount++;
    }

    /**
     * @param array<string, mixed> $regData
     */
    protected function upsertBroker(array $regData): Broker
    {
        $broker = Broker::query()
            ->where('broker_name', $regData['ru013'] ?? null)
            ->where('broker_mail', $regData['ru017'] ?? null)
            ->first();

        $payload = [
            'itanji_id' => $regData['ru012'] ?? null,
            'broker_name' => $regData['ru013'] ?? null,
            'broker_name_kana' => $regData['ru014'] ?? null,
            'broker_mobile_tel' => $regData['ru015'] ?? null,
            'broker_tantou_name' => $regData['ru016'] ?? null,
            'broker_mail' => $regData['ru017'] ?? null,
            'broker_fax' => $regData['ru018'] ?? null,
            'broker_tel' => $regData['ru019'] ?? null,
            'broker_zip' => $regData['ru020'] ?? null,
            'broker_address' => $regData['ru021'] ?? null,
            'area_id' => $this->resolveAreaId($regData['ru021'] ?? ''),
        ];

        if ($broker) {
            $broker->fill($payload);
            $broker->save();
            return $broker;
        }

        return Broker::query()->create($payload);
    }

    protected function resolveAreaId(string $address): int
    {
        $areaId = 0;

        if (preg_match('/千代田区|港区|中央区|新宿区|渋谷区|文京区/u', $address) === 1) {
            $areaId = 8;
        }
        if (preg_match('/港区|品川区|目黒区|大田区/u', $address) === 1) {
            $areaId = 5;
        }
        if (preg_match('/世田谷区|渋谷区|中野区|杉並区|練馬区/u', $address) === 1) {
            $areaId = 4;
        }
        if (preg_match('/豊島区|北区|荒川区|板橋区|足立区/u', $address) === 1) {
            $areaId = 6;
        }
        if (preg_match('/台東区|墨田区|江東区|葛飾区|江戸川区/u', $address) === 1) {
            $areaId = 3;
        }
        if ($areaId === 0 && preg_match('/東京都/u', $address) === 1) {
            $areaId = 9;
        }
        if (preg_match('/千葉県/u', $address) === 1) {
            $areaId = 7;
        }
        if (preg_match('/横浜市/u', $address) === 1) {
            $areaId = 11;
        }
        if (preg_match('/川崎市/u', $address) === 1) {
            $areaId = 10;
        }
        if (preg_match('/さいたま市|川口市|鴻巣市|上尾市|蕨市|戸田市|桶川市|北本市|伊奈町/u', $address) === 1) {
            $areaId = 1;
        }
        if (preg_match('/川越市|所沢市|飯能市|東松山市|狭山市|入間市|朝霞市|志木市|和光市|新座市|富士見市|ふじみ野市|坂戸市|鶴ヶ島市|日高市|三芳町|毛呂山町|越生町|滑川町|嵐山町|小川町|ときがわ町|川島町|吉見町|鳩山町|東秩父村/u', $address) === 1) {
            $areaId = 2;
        }

        if ($areaId === 0) {
            $areaId = 12;
        }

        return $areaId;
    }

    protected function insertLog($row)
    {
        $row = array_map(static function ($value) {
            return $value === '' ? null : $value;
        }, $row);

        CorporateTenancyApplicationLog::create([
            'import_date' => now(),
            'application_id' => $row[0] ?? null,
            'shop_name' => $row[1] ?? null,
            'property_name' => $row[2] ?? null,
            'room_number' => $row[3] ?? null,
            'room_key' => $row[4] ?? null,
            'building_key' => $row[5] ?? null,
            'itanji_account_id' => $row[6] ?? null,
            'management_company_id' => $row[7] ?? null,
            'room_id' => $row[8] ?? null,
            'application_status_id' => $row[9] ?? null,
            'confirmation_status' => $row[10] ?? null,
            'broker_itanji_account_id' => $row[11] ?? null,
            'broker_company_name' => $row[12] ?? null,
            'broker_company_name_kana' => $row[13] ?? null,
            'broker_mobile_phone' => $row[14] ?? null,
            'broker_staff_name' => $row[15] ?? null,
            'broker_email' => $row[16] ?? null,
            'broker_fax' => $row[17] ?? null,
            'broker_phone' => $row[18] ?? null,
            'broker_zip' => $row[19] ?? null,
            'broker_address' => $row[20] ?? null,
            'guarantor_company_plan_id' => $row[21] ?? null,
            'corporation_flag' => $row[22] ?? null,
            'paper_input_flag' => $row[23] ?? null,
            'proxy_application_flag' => $row[24] ?? null,
            'guarantor_reexamination_flag' => $row[25] ?? null,
            'applicant_editable_flag' => $row[26] ?? null,
            'staff_id' => $row[27] ?? null,
            'guarantor_auto_link_flag' => $row[28] ?? null,
            'contract_number' => $row[29] ?? null,
            'application_created_at' => $row[30] ?? null,
            'application_updated_at' => $row[31] ?? null,
            'guarantor_company_name' => $row[32] ?? null,
            'guarantor_plan_name' => $row[33] ?? null,
            'screening_result' => $row[34] ?? null,
            'guarantee_number' => $row[35] ?? null,
            'guarantee_target_amount' => $row[36] ?? null,
            'initial_guarantee_fee' => $row[37] ?? null,
            'guarantee_order' => $row[38] ?? null,
            'applicant_email' => $row[39] ?? null,
            'applicant_first_name' => $row[40] ?? null,
            'applicant_last_name' => $row[41] ?? null,
            'applicant_full_name' => $row[42] ?? null,
            'property_usage_type' => $row[43] ?? null,
            'property_name_detail' => $row[44] ?? null,
            'property_name_kana' => $row[45] ?? null,
            'property_room_number' => $row[46] ?? null,
            'property_address' => $row[47] ?? null,
            'rent' => $row[48] ?? null,
            'management_fee' => $row[49] ?? null,
            'utilities_fee' => $row[50] ?? null,
            'neighborhood_fee' => $row[51] ?? null,
            'management_transfer_fee' => $row[52] ?? null,
            'parking_fee' => $row[53] ?? null,
            'other_fixed_fee' => $row[54] ?? null,
            'total_monthly_payment' => $row[55] ?? null,
            'deposit' => $row[56] ?? null,
            'security_deposit' => $row[57] ?? null,
            'planned_move_in_date' => $row[58] ?? null,
            'desired_move_in_date' => $row[59] ?? null,
            'desired_contract_date' => $row[60] ?? null,
            'initial_payment_due_date' => $row[61] ?? null,
            'tokio_marine_insurance_flag' => $row[62] ?? null,
            'corporate_name' => $row[63] ?? null,
            'corporate_name_kana' => $row[64] ?? null,
            'corporate_head_office_postal_code' => $row[65] ?? null,
            'corporate_head_office_prefecture' => $row[66] ?? null,
            'corporate_head_office_city' => $row[67] ?? null,
            'corporate_head_office_address' => $row[68] ?? null,
            'corporate_head_office_building' => $row[69] ?? null,
            'corporate_head_office_phone' => $row[70] ?? null,
            'corporate_head_office_fax' => $row[71] ?? null,
            'corporate_email' => $row[72] ?? null,
            'corporate_industry' => $row[73] ?? null,
            'corporate_capital' => $row[74] ?? null,
            'corporate_employee_count' => $row[75] ?? null,
            'corporate_established_date' => $row[76] ?? null,
            'corporate_representative_last_name' => $row[77] ?? null,
            'corporate_representative_first_name' => $row[78] ?? null,
            'corporate_representative_last_name_kana' => $row[79] ?? null,
            'corporate_representative_first_name_kana' => $row[80] ?? null,
            'corporate_representative_mobile_phone' => $row[81] ?? null,
            'corporate_representative_postal_code' => $row[82] ?? null,
            'corporate_representative_prefecture' => $row[83] ?? null,
            'corporate_representative_city' => $row[84] ?? null,
            'corporate_representative_address' => $row[85] ?? null,
            'corporate_representative_building' => $row[86] ?? null,
            'corporate_contact_last_name' => $row[87] ?? null,
            'corporate_contact_first_name' => $row[88] ?? null,
            'corporate_contact_last_name_kana' => $row[89] ?? null,
            'corporate_contact_first_name_kana' => $row[90] ?? null,
            'corporate_contact_department' => $row[91] ?? null,
            'corporate_contact_phone' => $row[92] ?? null,
            'occupant1_last_name' => $row[93] ?? null,
            'occupant1_first_name' => $row[94] ?? null,
            'occupant1_last_name_kana' => $row[95] ?? null,
            'occupant1_first_name_kana' => $row[96] ?? null,
            'occupant1_relationship' => $row[97] ?? null,
            'occupant1_birth_date' => $row[98] ?? null,
            'occupant1_age' => $row[99] ?? null,
            'occupant1_mobile_phone' => $row[100] ?? null,
            'occupant1_annual_income' => $row[101] ?? null,
            'occupant2_last_name' => $row[102] ?? null,
            'occupant2_first_name' => $row[103] ?? null,
            'occupant2_last_name_kana' => $row[104] ?? null,
            'occupant2_first_name_kana' => $row[105] ?? null,
            'occupant2_relationship' => $row[106] ?? null,
            'occupant2_birth_date' => $row[107] ?? null,
            'occupant2_age' => $row[108] ?? null,
            'occupant2_mobile_phone' => $row[109] ?? null,
            'occupant2_annual_income' => $row[110] ?? null,
            'occupant3_last_name' => $row[111] ?? null,
            'occupant3_first_name' => $row[112] ?? null,
            'occupant3_last_name_kana' => $row[113] ?? null,
            'occupant3_first_name_kana' => $row[114] ?? null,
            'occupant3_relationship' => $row[115] ?? null,
            'occupant3_birth_date' => $row[116] ?? null,
            'occupant3_age' => $row[117] ?? null,
            'occupant3_mobile_phone' => $row[118] ?? null,
            'occupant3_annual_income' => $row[119] ?? null,
            'occupant4_last_name' => $row[120] ?? null,
            'occupant4_first_name' => $row[121] ?? null,
            'occupant4_last_name_kana' => $row[122] ?? null,
            'occupant4_first_name_kana' => $row[123] ?? null,
            'occupant4_relationship' => $row[124] ?? null,
            'occupant4_birth_date' => $row[125] ?? null,
            'occupant4_age' => $row[126] ?? null,
            'occupant4_mobile_phone' => $row[127] ?? null,
            'occupant4_annual_income' => $row[128] ?? null,
            'emergency_contact_last_name' => $row[129] ?? null,
            'emergency_contact_first_name' => $row[130] ?? null,
            'emergency_contact_last_name_kana' => $row[131] ?? null,
            'emergency_contact_first_name_kana' => $row[132] ?? null,
            'emergency_contact_gender' => $row[133] ?? null,
            'emergency_contact_birth_date' => $row[134] ?? null,
            'emergency_contact_age' => $row[135] ?? null,
            'emergency_contact_relationship' => $row[136] ?? null,
            'emergency_contact_mobile_phone' => $row[137] ?? null,
            'emergency_contact_home_phone' => $row[138] ?? null,
            'emergency_contact_zip' => $row[139] ?? null,
            'emergency_contact_prefecture' => $row[140] ?? null,
            'emergency_contact_city' => $row[141] ?? null,
            'emergency_contact_address' => $row[142] ?? null,
            'emergency_contact_building' => $row[143] ?? null,
            'emergency_contact_company_name' => $row[144] ?? null,
            'emergency_contact_company_name_kana' => $row[145] ?? null,
            'emergency_contact_work_phone' => $row[146] ?? null,
            'guarantor_last_name' => $row[147] ?? null,
            'guarantor_first_name' => $row[148] ?? null,
            'guarantor_last_name_kana' => $row[149] ?? null,
            'guarantor_first_name_kana' => $row[150] ?? null,
            'guarantor_gender' => $row[151] ?? null,
            'guarantor_relationship' => $row[152] ?? null,
            'guarantor_birth_date' => $row[153] ?? null,
            'guarantor_age' => $row[154] ?? null,
            'guarantor_mobile_phone' => $row[155] ?? null,
            'guarantor_home_phone' => $row[156] ?? null,
            'guarantor_zip' => $row[157] ?? null,
            'guarantor_prefecture' => $row[158] ?? null,
            'guarantor_city' => $row[159] ?? null,
            'guarantor_address' => $row[160] ?? null,
            'guarantor_building' => $row[161] ?? null,
            'guarantor_residence_type' => $row[162] ?? null,
            'guarantor_job' => $row[163] ?? null,
            'guarantor_workplace_name' => $row[164] ?? null,
            'guarantor_workplace_name_kana' => $row[165] ?? null,
            'guarantor_workplace_phone' => $row[166] ?? null,
            'guarantor_workplace_zip' => $row[167] ?? null,
            'guarantor_workplace_prefecture' => $row[168] ?? null,
            'guarantor_workplace_city' => $row[169] ?? null,
            'guarantor_workplace_address' => $row[170] ?? null,
            'guarantor_workplace_building' => $row[171] ?? null,
            'guarantor_workplace_industry' => $row[172] ?? null,
            'guarantor_workplace_capital' => $row[173] ?? null,
            'guarantor_workplace_employee_count' => $row[174] ?? null,
            'guarantor_workplace_established_date' => $row[175] ?? null,
            'guarantor_annual_income' => $row[176] ?? null,
            'guarantor_years_employed' => $row[177] ?? null,
            'corporate_company_registry_document' => $row[178] ?? null,
            'created_user_id' => $row[179] ?? null,
            'user_created_at' => $row[180] ?? null,
            'updated_user_id' => $row[181] ?? null,
            'user_updated_at' => $row[182] ?? null,
            'deleted_user_id' => $row[183] ?? null,
            'user_deleted_at' => $row[184] ?? null,
        ]);
    }

    /**
     * @param array<string, mixed> $regData
     */
    protected function upsertEnProgress(array $regData): void
    {
        $progress = Progress::firstOrCreate([
            'investment_id' => $regData['investment_id'],
            'investment_room_uid' => $regData['investment_room_uid'],
            'complete_date' => null
        ]);
        $progress->en_responsible_id = $regData['en_staff_id'] ?? null;
        $progress->keiyaku_shiki_date = $regData['ru060'] ?? null;
        $progress->mousikomi_date = $regData['ru031'] ?? null;
        $progress->save();

        // 保証会社データ取得
        $guaranteeCompany = GuaranteeCompany::firstOrCreate([
            'company_name' =>  $regData['ru033']
        ]);

        $enProgress = EnProgress::firstOrNew(['application_id' => $regData['ru001'] ?? null]);
        $enProgress->progress_id = $progress->id;
        $enProgress->responsible_user_id = $regData['en_staff_id'] ?? null;
        $enProgress->broker_id = $regData['broker_id'] ?? null;

        $enProgress->application_id = $regData['ru001'] ?? null;                // 申込ID
        $enProgress->applicant_type = EnProgress::APPLICANT_TYPE_CORPORATE;     // 申込人種別
        $enProgress->application_date = $regData['ru031'] ?? null;              // 申込作成日時
        if ($enProgress->application_date) $enProgress->application_date_state = 1;

        $enProgress->guarantee_company_id = $guaranteeCompany->id;              // 保証会社ID
        $enProgress->guarantee_company_plan = $regData['ru034'] ?? null;        // 保証会社プラン名
        $enProgress->screening_result = ($regData['ru035'] ?? null) === '承認' ? EnProgress::SCREENING_RESULT_APPROVED : null;   // 審査結果
        $enProgress->approval_number = $regData['ru036'] ?? null;               // 保証番号
        $enProgress->priority_order = $regData['ru039'] ?? null;                // 番手
        $enProgress->rent_fee = $regData['ru049'] ?? null;                      // 賃貸物件内容家賃
        $enProgress->common_service_fee = $regData['ru050'] ?? null;            // 賃貸物件内容管理費／共益費
        $enProgress->water_fee = $regData['ru051'] ?? null;                     // 賃貸物件内容水道光熱費
        $enProgress->neighborhood_fee = $regData['ru052'] ?? null;              // 賃貸物件内容町内会費（区費）
        $enProgress->transfer_fee = $regData['ru053'] ?? null;                  // その他・管理会社振替手数料（管理会社）
        $enProgress->parking_fee = $regData['ru054'] ?? null;                   // 賃貸物件内容駐車場料金
        $enProgress->other_fixed_fee = $regData['ru055'] ?? null;               // 賃貸物件内容その他固定費
        $enProgress->deposit_fee = $regData['ru057'] ?? null;                   // 賃貸物件内容敷金
        $enProgress->security_deposit_fee = $regData['ru058'] ?? null;          // 賃貸物件内容保証金
        $enProgress->desired_move_in_date = $regData['ru060'] ?? null;          // 賃貸物件内容入居希望日
        $enProgress->desired_contract_date = $regData['ru061'] ?? null;         // 賃貸物件内容契約希望日
        $enProgress->planned_payment_date = $regData['ru062'] ?? null;          // 賃貸物件内容初期費用入金予定日
        $enProgress->resetNextAction();
        $enProgress->save();

        $enProgressCorporateApplicant = EnProgressCorporateApplicant::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressCorporateApplicant->en_progress_id = $enProgress->id;
        $enProgressCorporateApplicant->company_name = $regData['ru064'] ?? null;                        // 法人の申込者会社名
        $enProgressCorporateApplicant->company_kana = $regData['ru065'] ?? null;                        // 法人の申込者会社名（カナ）
        $enProgressCorporateApplicant->head_office_postal_code = $regData['ru066'] ?? null;             // 法人の会社情報本社所在地（郵便番号）
        $enProgressCorporateApplicant->head_office_prefecture = $regData['ru067'] ?? null;              // 法人の会社情報本社所在地（都道府県）
        $enProgressCorporateApplicant->head_office_city = $regData['ru068'] ?? null;                    // 法人の会社情報本社所在地（市区町村）
        $enProgressCorporateApplicant->head_office_street = $regData['ru069'] ?? null;                  // 法人の会社情報本社所在地（番地・丁目）
        $enProgressCorporateApplicant->head_office_building = $regData['ru070'] ?? null;                // 法人の会社情報本社所在地（建物名・部屋番号）
        $enProgressCorporateApplicant->head_office_phone_number = $regData['ru071'] ?? null;            // 法人の会社情報本社電話番号
        $enProgressCorporateApplicant->head_office_fax_number = $regData['ru072'] ?? null;              // 法人の会社情報本社FAX番号
        $enProgressCorporateApplicant->email = $regData['ru073'] ?? null;                               // 法人の申込者メールアドレス
        $enProgressCorporateApplicant->industry = $regData['ru074'] ?? null;                            // 法人の会社情報業種
        $enProgressCorporateApplicant->capital = $regData['ru075'] ?? null;                             // 法人の会社情報資本金
        $enProgressCorporateApplicant->number_of_employees = $regData['ru076'] ?? null;                 // 法人の会社情報従業員数
        $enProgressCorporateApplicant->established_date = $regData['ru077'] ?? null;                    // 法人の会社情報設立年月日
        $enProgressCorporateApplicant->representative_last_name = $regData['ru078'] ?? null;            // 法人の会社代表者氏名（名字）
        $enProgressCorporateApplicant->representative_first_name = $regData['ru079'] ?? null;           // 法人の会社代表者氏名（名字）
        $enProgressCorporateApplicant->representative_last_kana = $regData['ru080'] ?? null;            // 法人の会社代表者氏名（名字カナ）
        $enProgressCorporateApplicant->representative_first_kana = $regData['ru081'] ?? null;           // 法人の会社代表者氏名（名前カナ）
        $enProgressCorporateApplicant->representative_mobile_phone_number = $regData['ru082'] ?? null;  // 法人の会社代表者携帯電話番号
        $enProgressCorporateApplicant->representative_postal_code = $regData['ru083'] ?? null;          // 法人の会社代表者現住所（郵便番号）
        $enProgressCorporateApplicant->representative_prefecture = $regData['ru084'] ?? null;           // 法人の会社代表者現住所（都道府県）
        $enProgressCorporateApplicant->representative_city = $regData['ru085'] ?? null;                 // 法人の会社代表者現住所（市区町村）
        $enProgressCorporateApplicant->representative_street = $regData['ru086'] ?? null;               // 法人の会社代表者現住所（番地・丁目）
        $enProgressCorporateApplicant->representative_building = $regData['ru087'] ?? null;             // 法人の会社代表者現住所（建物名・部屋番号）
        $enProgressCorporateApplicant->contact_last_name = $regData['ru088'] ?? null;                   // 法人の申込者担当者名（名字）
        $enProgressCorporateApplicant->contact_first_name = $regData['ru089'] ?? null;                  // 法人の申込者担当者名（名前）
        $enProgressCorporateApplicant->contact_last_kana = $regData['ru090'] ?? null;                   // 法人の申込者担当者名（名字カナ）
        $enProgressCorporateApplicant->contact_first_kana = $regData['ru091'] ?? null;                  // 法人の申込者担当者名（名前カナ）
        $enProgressCorporateApplicant->contact_department = $regData['ru092'] ?? null;                  // 法人の申込者担当者所属部署
        $enProgressCorporateApplicant->contact_phone_number = $regData['ru093'] ?? null;                // 法人の申込者担当者電話番号
        $enProgressCorporateApplicant->save();

        $enProgressOccupants = EnProgressOccupants::firstOrNew([
            'en_progress_id' => $enProgress->id,
            'occupant_seq' => 1
        ]);
        $enProgressOccupants->en_progress_id = $enProgress->id;
        $enProgressOccupants->last_name = $regData['ru094'] ?? null;                    // 法人の入居者1氏名（名字）
        $enProgressOccupants->first_name = $regData['ru095'] ?? null;                   // 法人の入居者1氏名（名前）
        $enProgressOccupants->last_kana = $regData['ru096'] ?? null;                    // 法人の入居者1氏名（名字カナ）
        $enProgressOccupants->first_kana = $regData['ru097'] ?? null;                   // 法人の入居者1氏名（名前カナ）
        $enProgressOccupants->relationship = $regData['ru098'] ?? null;                 // 法人の入居者1続柄
        $enProgressOccupants->birth_date = $regData['ru099'] ?? null;                   // 法人の入居者1生年月日
        $enProgressOccupants->mobile_phone_number = $regData['ru101'] ?? null;          // 法人の入居者1携帯電話番号
        $enProgressOccupants->annual_income = $regData['ru102'] ?? null;                // 法人の入居者1税込年収
        $enProgressOccupants->save();

        // 入居者2
        if ($regData['ru103'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 2
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru103'] ?? null;                    // 法人の入居者2氏名（名字）
            $enProgressOccupants->first_name = $regData['ru104'] ?? null;                   // 法人の入居者2氏名（名前）
            $enProgressOccupants->last_kana = $regData['rru105'] ?? null;                   // 法人の入居者2氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru106'] ?? null;                   // 法人の入居者2氏名（名前カナ）
            $enProgressOccupants->relationship = $regData['ru107'] ?? null;                 // 法人の入居者2続柄
            $enProgressOccupants->birth_date = $regData['ru108'] ?? null;                   // 法人の入居者2生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru110'] ?? null;          // 法人の入居者2携帯電話番号
            $enProgressOccupants->annual_income = $regData['ru111'] ?? null;                // 法人の入居者2税込年収
            $enProgressOccupants->save();
        }

        // 入居者3
        if ($regData['ru112'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 3
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru112'] ?? null;                    // 法人の入居者3氏名（名字）
            $enProgressOccupants->first_name = $regData['ru113'] ?? null;                   // 法人の入居者3氏名（名前）
            $enProgressOccupants->last_kana = $regData['ru114'] ?? null;                    // 法人の入居者3氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru115'] ?? null;                   // 法人の入居者3氏名（名前カナ）
            $enProgressOccupants->relationship = $regData['ru116'] ?? null;                 // 法人の入居者3続柄
            $enProgressOccupants->birth_date = $regData['ru117'] ?? null;                   // 法人の入居者3生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru119'] ?? null;          // 法人の入居者3携帯電話番号
            $enProgressOccupants->annual_income = $regData['ru120'] ?? null;                // 法人の入居者3税込年収
            $enProgressOccupants->save();
        }

        // 入居者4
        if ($regData['ru121'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 4
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru121'] ?? null;                    // 法人の入居者4氏名（名字）
            $enProgressOccupants->first_name = $regData['ru122'] ?? null;                   // 法人の入居者4氏名（名前）
            $enProgressOccupants->last_kana = $regData['ru123'] ?? null;                    // 法人の入居者4氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru124'] ?? null;                   // 法人の入居者4氏名（名前カナ）
            $enProgressOccupants->relationship = $regData['ru125'] ?? null;                 // 法人の入居者4続柄
            $enProgressOccupants->birth_date = $regData['ru126'] ?? null;                   // 法人の入居者4生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru128'] ?? null;          // 法人の入居者4携帯電話番号
            $enProgressOccupants->annual_income = $regData['ru129'] ?? null;                // 法人の入居者4税込年収
            $enProgressOccupants->save();
        }

        $enProgressEmergencyContact = EnProgressEmergencyContact::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressEmergencyContact->en_progress_id = $enProgress->id;
        $enProgressEmergencyContact->last_name = $regData['ru130'] ?? null;                         // 法人の緊急連絡先氏名（名字）
        $enProgressEmergencyContact->first_name = $regData['ru131'] ?? null;                        // 法人の緊急連絡先氏名（名前）
        $enProgressEmergencyContact->last_kana = $regData['ru132'] ?? null;                         // 法人の緊急連絡先氏名（名字カナ）
        $enProgressEmergencyContact->first_kana = $regData['ru133'] ?? null;                        // 法人の緊急連絡先氏名（名前カナ）
        $enProgressEmergencyContact->gender = $this->getGender($regData['ru134'] ?? null, EnProgressEmergencyContact::class);     // 法人の緊急連絡先性別
        $enProgressEmergencyContact->birth_date = $regData['ru135'] ?? null;                        // 法人の緊急連絡先生年月日
        $enProgressEmergencyContact->relationship = $regData['ru137'] ?? null;                      // 法人の緊急連絡先続柄
        $enProgressEmergencyContact->mobile_phone_number = $regData['ru138'] ?? null;               // 法人の緊急連絡先携帯電話番号
        $enProgressEmergencyContact->phone_number = $regData['ru139'] ?? null;                      // 法人の緊急連絡先自宅電話番号
        $enProgressEmergencyContact->postal_code = $regData['ru140'] ?? null;                       // 法人の緊急連絡先自宅住所（郵便番号）
        $enProgressEmergencyContact->prefecture = $regData['ru141'] ?? null;                        // 法人の緊急連絡先自宅住所（都道府県）
        $enProgressEmergencyContact->city = $regData['ru142'] ?? null;                              // 法人の緊急連絡先自宅住所（市区町村）
        $enProgressEmergencyContact->street = $regData['ru143'] ?? null;                            // 法人の緊急連絡先自宅住所（番地・丁目）
        $enProgressEmergencyContact->building = $regData['ru144'] ?? null;                          // 法人の緊急連絡先自宅住所（建物名・部屋番号）
        $enProgressEmergencyContact->workplace_or_school_name = $regData['ru145'] ?? null;          // 法人の緊急連絡先勤務先名
        $enProgressEmergencyContact->workplace_or_school_kana = $regData['ru146'] ?? null;          // 法人の緊急連絡先勤務先名（カナ）
        $enProgressEmergencyContact->workplace_or_school_phone_number = $regData['ru147'] ?? null;  // 法人の緊急連絡先勤務先電話番号
        $enProgressEmergencyContact->save();

        $enProgressGuarantor = EnProgressGuarantor::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressGuarantor->en_progress_id = $enProgress->id;
        $enProgressGuarantor->last_name = $regData['ru148'] ?? null;                            // 法人の連帯保証人氏名（名字）
        $enProgressGuarantor->first_name = $regData['ru149'] ?? null;                           // 法人の連帯保証人氏名（名前）
        $enProgressGuarantor->last_kana = $regData['ru150'] ?? null;                            // 法人の連帯保証人氏名（名字カナ）
        $enProgressGuarantor->first_kana = $regData['ru151'] ?? null;                           // 法人の連帯保証人氏名（名前カナ）
        $enProgressGuarantor->gender = $this->getGender($regData['ru152'] ?? null, EnProgressGuarantor::class);     // 法人の連帯保証人性別
        $enProgressGuarantor->relationship = $regData['ru153'] ?? null;                         // 法人の連帯保証人続柄
        $enProgressGuarantor->birth_date = $regData['ru154'] ?? null;                           // 法人の連帯保証人生年月日
        $enProgressGuarantor->mobile_phone_number = $regData['ru156'] ?? null;                  // 法人の連帯保証人携帯電話番号
        $enProgressGuarantor->phone_number = $regData['ru157'] ?? null;                         // 法人の連帯保証人自宅電話番号
        $enProgressGuarantor->postal_code = $regData['ru158'] ?? null;                          // 法人の連帯保証人現住所（郵便番号）
        $enProgressGuarantor->prefecture = $regData['ru159'] ?? null;                           // 法人の連帯保証人現住所（都道府県）
        $enProgressGuarantor->city = $regData['ru160'] ?? null;                                 // 法人の連帯保証人現住所（市区町村）
        $enProgressGuarantor->street = $regData['ru161'] ?? null;                               // 法人の連帯保証人現住所（番地・丁目）
        $enProgressGuarantor->building = $regData['ru162'] ?? null;                             // 法人の連帯保証人現住所（建物名・部屋番号）
        $enProgressGuarantor->residence_type = $regData['ru163'] ?? null;                       // 法人の連帯保証人住居種別
        $enProgressGuarantor->occupation = $regData['ru164'] ?? null;                           // 法人の連帯保証人お勤め先職業
        $enProgressGuarantor->workplace_name = $regData['ru165'] ?? null;                       // 法人の連帯保証人お勤め先勤務先/学校名
        $enProgressGuarantor->workplace_kana = $regData['ru166'] ?? null;                       // 法人の連帯保証人お勤め先勤務先/学校名（カナ）
        $enProgressGuarantor->workplace_phone_number = $regData['ru167'] ?? null;               // 法人の連帯保証人お勤め先勤務先電話番号
        $enProgressGuarantor->workplace_postal_code = $regData['ru168'] ?? null;                // 法人の連帯保証人お勤め先勤務先所在地（郵便番号）
        $enProgressGuarantor->workplace_prefecture = $regData['ru169'] ?? null;                 // 法人の連帯保証人お勤め先勤務先所在地（都道府県）
        $enProgressGuarantor->workplace_city = $regData['ru170'] ?? null;                       // 法人の連帯保証人お勤め先勤務先所在地（市区町村）
        $enProgressGuarantor->workplace_street = $regData['ru171'] ?? null;                     // 法人の連帯保証人お勤め先勤務先所在地（番地・丁目）
        $enProgressGuarantor->workplace_building = $regData['ru172'] ?? null;                   // 法人の連帯保証人お勤め先勤務先所在地（建物名・部屋番号）
        $enProgressGuarantor->industry = $regData['ru173'] ?? null;                             // 法人の連帯保証人お勤め先業種
        $enProgressGuarantor->capital = $regData['ru174'] ?? null;                              // 法人の連帯保証人お勤め先資本金
        $enProgressGuarantor->number_of_employees = $regData['ru175'] ?? null;                  // 法人の連帯保証人お勤め先従業員数
        $enProgressGuarantor->established_date = $regData['ru176'] ?? null;                     // 法人の連帯保証人お勤め先設立年月日
        $enProgressGuarantor->annual_income = $regData['ru177'] ?? null;                        // 法人の連帯保証人お勤め先税込年収
        $enProgressGuarantor->years_of_service = $regData['ru178'] ?? null;                     // 法人の連帯保証人お勤め先勤続年数
        $enProgressGuarantor->save();
    }

    protected function getGender($value, $class) {
        $gender = null;

        switch ($value) {
            case '男':
            case '男性':
                $gender = $class::GENDER_MALE;
                break;
            case '女':
            case '女性':
                $gender = $class::GENDER_FEMALE;
                break;
        }

        return $gender;
    }

    public function render()
    {
        return view('livewire.admin.import.corporate-tenancy-application-import');
    }
}
