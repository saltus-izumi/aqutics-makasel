<?php

namespace App\Livewire\Admin\Import;

use App\Models\Broker;
use App\Models\BrokerInvestment;
use App\Models\EnProgress;
use App\Models\EnProgressEmergencyContact;
use App\Models\EnProgressIndividualApplicant;
use App\Models\EnProgressOccupants;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\PersonalTenancyApplicationLog;
use App\Models\Progress;
use App\Models\ReactionPersonal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonalTenancyApplicationImport extends Component
{
    use WithFileUploads;

    public $personalTenancyApplicationFile = null;

    public ?int $readCount = null;
    public ?int $insertResidentCount = null;
    public ?int $updateResidentCount = null;
    public ?int $errorCount = null;
    public array $errorMessages = [];

    protected $messages = [
        'personalTenancyApplicationFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        $this->validate([
            'personalTenancyApplicationFile' => ['required', 'file'],
        ]);

        $this->readCount = 0;
        $this->insertResidentCount = 0;
        $this->updateResidentCount = 0;
        $this->errorCount = 0;
        $this->errorMessages = [];

        $originalName = $this->personalTenancyApplicationFile->getClientOriginalName();
        if (!preg_match('/^個人申込-([0-9]{8})\.csv$/u', $originalName, $matches)) {
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
            ReactionPersonal::query()
                ->whereDate('sampling_date', $samplingDate->toDateString())
                ->delete();

            $csv = new \SplFileObject($this->personalTenancyApplicationFile->getRealPath(), 'r');
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

                $regData = $this->mapCsvRow($record);
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
                    if (!$investment) {
                        $this->errorCount = ($this->errorCount ?? 0) + 1;
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s に該当する物件情報がありません。',
                            $rowNo,
                            $regData['ru003'] ?? ''
                        );
                    } else {
                        $this->errorCount = ($this->errorCount ?? 0) + 1;
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s %s に該当する部屋情報がありません。',
                            $rowNo,
                            $regData['ru003'] ?? '',
                            $regData['ru004'] ?? ''
                        );
                    }
                    continue;
                }

                ReactionPersonal::query()->create($regData);

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

        $this->reset('personalTenancyApplicationFile');
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
    protected function mapCsvRow(array $row): array
    {
        $mapped = [];
        for ($i = 0; $i <= 187; $i++) {
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
            ->where('investment_room_uid', $room->id)
            ->first();

        $genderId = 0;
        if (($regData['ru066'] ?? '') === '男') {
            $genderId = 1;
        } elseif (($regData['ru066'] ?? '') === '女') {
            $genderId = 2;
        }

        $attributeId = 0;
        if (($regData['ru079'] ?? '') === '正社員') {
            $attributeId = 1;
        }
        if (($regData['ru079'] ?? '') === '学生') {
            $attributeId = 2;
        }

        $payload = [
            'investment_id' => $room->investment_id,
            'investment_uid' => $room->id,
            'investment_room_id' => $room->investment_room_id,
            'contractor_name' => ($regData['ru062'] ?? '') . '　' . ($regData['ru063'] ?? ''),
            'gender_id' => $genderId,
            'age' => $this->calculateAgeFromBirthDate($regData['ru068'] ?? null),
            'attribute_id' => $attributeId,
            'workplace' => $regData['ru080'] ?? null,
            'annual_income' => $regData['ru092'] ?? null,
        ];

        if (isset($regData['ru154'], $regData['ru155'])) {
            $payload['guarantor_name'] = $regData['ru154'] . '　' . $regData['ru155'];
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

    protected function calculateAgeFromBirthDate(mixed $birthDate): ?int
    {
        if (!is_string($birthDate)) {
            return null;
        }

        $birthDate = trim($birthDate);
        if ($birthDate === '') {
            return null;
        }

        try {
            $birthday = Carbon::parse($birthDate)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        if ($birthday->isFuture()) {
            return null;
        }

        return $birthday->age;
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

        PersonalTenancyApplicationLog::create([
            'import_at' => now(),
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
            'desired_move_in_date' => $row[58] ?? null,
            'desired_contract_date' => $row[59] ?? null,
            'initial_payment_due_date' => $row[60] ?? null,
            'tokio_marine_insurance_flag' => $row[61] ?? null,
            'applicant_last_name_kanji' => $row[62] ?? null,
            'applicant_first_name_kanji' => $row[63] ?? null,
            'applicant_last_name_kana' => $row[64] ?? null,
            'applicant_first_name_kana' => $row[65] ?? null,
            'applicant_gender' => $row[66] ?? null,
            'applicant_birth_date' => $row[67] ?? null,
            'applicant_age' => $row[68] ?? null,
            'applicant_mobile_phone' => $row[69] ?? null,
            'applicant_email_address' => $row[70] ?? null,
            'applicant_home_phone' => $row[71] ?? null,
            'applicant_zip' => $row[72] ?? null,
            'applicant_prefecture' => $row[73] ?? null,
            'applicant_city' => $row[74] ?? null,
            'applicant_address_line' => $row[75] ?? null,
            'applicant_building' => $row[76] ?? null,
            'applicant_residence_type' => $row[77] ?? null,
            'applicant_residence_years' => $row[78] ?? null,
            'applicant_move_reason' => $row[79] ?? null,
            'applicant_job' => $row[80] ?? null,
            'applicant_company_name' => $row[81] ?? null,
            'applicant_company_name_kana' => $row[82] ?? null,
            'applicant_company_phone' => $row[83] ?? null,
            'applicant_company_zip' => $row[84] ?? null,
            'applicant_company_prefecture' => $row[85] ?? null,
            'applicant_company_city' => $row[86] ?? null,
            'applicant_company_address' => $row[87] ?? null,
            'applicant_company_building' => $row[88] ?? null,
            'applicant_industry' => $row[89] ?? null,
            'applicant_company_established_date' => $row[90] ?? null,
            'applicant_company_capital' => $row[91] ?? null,
            'applicant_years_employed' => $row[92] ?? null,
            'applicant_annual_income' => $row[93] ?? null,
            'occupant1_last_name' => $row[94] ?? null,
            'occupant1_first_name' => $row[95] ?? null,
            'occupant1_last_name_kana' => $row[96] ?? null,
            'occupant1_first_name_kana' => $row[97] ?? null,
            'occupant1_gender' => $row[98] ?? null,
            'occupant1_relationship' => $row[99] ?? null,
            'occupant1_birth_date' => $row[100] ?? null,
            'occupant1_age' => $row[101] ?? null,
            'occupant1_mobile_phone' => $row[102] ?? null,
            'occupant1_company_name' => $row[103] ?? null,
            'occupant1_company_name_kana' => $row[104] ?? null,
            'occupant2_last_name' => $row[105] ?? null,
            'occupant2_first_name' => $row[106] ?? null,
            'occupant2_last_name_kana' => $row[107] ?? null,
            'occupant2_first_name_kana' => $row[108] ?? null,
            'occupant2_gender' => $row[109] ?? null,
            'occupant2_relationship' => $row[110] ?? null,
            'occupant2_birth_date' => $row[111] ?? null,
            'occupant2_age' => $row[112] ?? null,
            'occupant2_mobile_phone' => $row[113] ?? null,
            'occupant2_company_name' => $row[114] ?? null,
            'occupant2_company_name_kana' => $row[115] ?? null,
            'occupant3_last_name' => $row[116] ?? null,
            'occupant3_first_name' => $row[117] ?? null,
            'occupant3_last_name_kana' => $row[118] ?? null,
            'occupant3_first_name_kana' => $row[119] ?? null,
            'occupant3_gender' => $row[120] ?? null,
            'occupant3_relationship' => $row[121] ?? null,
            'occupant3_birth_date' => $row[122] ?? null,
            'occupant3_age' => $row[123] ?? null,
            'occupant3_mobile_phone' => $row[124] ?? null,
            'occupant3_company_name' => $row[125] ?? null,
            'occupant3_company_name_kana' => $row[126] ?? null,
            'occupant4_last_name' => $row[127] ?? null,
            'occupant4_first_name' => $row[128] ?? null,
            'occupant4_last_name_kana' => $row[129] ?? null,
            'occupant4_first_name_kana' => $row[130] ?? null,
            'occupant4_gender' => $row[131] ?? null,
            'occupant4_relationship' => $row[132] ?? null,
            'occupant4_birth_date' => $row[133] ?? null,
            'occupant4_age' => $row[134] ?? null,
            'occupant4_mobile_phone' => $row[135] ?? null,
            'occupant4_company_name' => $row[136] ?? null,
            'occupant4_company_name_kana' => $row[137] ?? null,
            'emergency_contact_last_name' => $row[138] ?? null,
            'emergency_contact_first_name' => $row[139] ?? null,
            'emergency_contact_last_name_kana' => $row[140] ?? null,
            'emergency_contact_first_name_kana' => $row[141] ?? null,
            'emergency_contact_gender' => $row[142] ?? null,
            'emergency_contact_birth_date' => $row[143] ?? null,
            'emergency_contact_age' => $row[144] ?? null,
            'emergency_contact_relationship' => $row[145] ?? null,
            'emergency_contact_mobile_phone' => $row[146] ?? null,
            'emergency_contact_home_phone' => $row[147] ?? null,
            'emergency_contact_zip' => $row[148] ?? null,
            'emergency_contact_prefecture' => $row[149] ?? null,
            'emergency_contact_city' => $row[150] ?? null,
            'emergency_contact_address' => $row[151] ?? null,
            'emergency_contact_building' => $row[152] ?? null,
            'emergency_contact_company_name' => $row[153] ?? null,
            'emergency_contact_company_name_kana' => $row[154] ?? null,
            'guarantor_last_name' => $row[155] ?? null,
            'guarantor_first_name' => $row[156] ?? null,
            'guarantor_last_name_kana' => $row[157] ?? null,
            'guarantor_first_name_kana' => $row[158] ?? null,
            'guarantor_gender' => $row[159] ?? null,
            'guarantor_birth_date' => $row[160] ?? null,
            'guarantor_age' => $row[161] ?? null,
            'guarantor_relationship' => $row[162] ?? null,
            'guarantor_mobile_phone' => $row[163] ?? null,
            'guarantor_home_phone' => $row[164] ?? null,
            'guarantor_zip' => $row[165] ?? null,
            'guarantor_prefecture' => $row[166] ?? null,
            'guarantor_city' => $row[167] ?? null,
            'guarantor_address' => $row[168] ?? null,
            'guarantor_building' => $row[169] ?? null,
            'guarantor_residence_type' => $row[170] ?? null,
            'guarantor_residence_years' => $row[171] ?? null,
            'guarantor_job' => $row[172] ?? null,
            'guarantor_company_name_kana' => $row[173] ?? null,
            'guarantor_company_phone' => $row[174] ?? null,
            'guarantor_company_zip' => $row[175] ?? null,
            'guarantor_company_prefecture' => $row[176] ?? null,
            'guarantor_company_city' => $row[177] ?? null,
            'guarantor_company_address' => $row[178] ?? null,
            'guarantor_company_building' => $row[179] ?? null,
            'guarantor_industry' => $row[180] ?? null,
            'guarantor_company_established_date' => $row[181] ?? null,
            'guarantor_company_capital' => $row[182] ?? null,
            'guarantor_annual_income' => $row[183] ?? null,
            'guarantor_years_employed' => $row[184] ?? null,
            'applicant_id_document_front' => $row[185] ?? null,
            'applicant_id_document_back' => $row[186] ?? null,
            'applicant_income_certificate' => $row[187] ?? null,
            'applicant_additional_document_1' => $row[188] ?? null,
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

        $enProgress = EnProgress::firstOrNew(['application_id' => $regData['ru001'] ?? null]);
        $enProgress->progress_id = $progress->id;
        $enProgress->responsible_user_id = $regData['en_staff_id'] ?? null;
        $enProgress->applicant_type = EnProgress::APPLICANT_TYPE_INDIVIDUAL;    // 申込人種別
        $enProgress->application_date = $regData['ru031'] ?? null;              // 申込作成日時
        $enProgress->guarantor_plan_name = $regData['ru034'] ?? null;           // 保証会社プラン名
        $enProgress->screening_result = ($regData['ru035'] ?? null) === '承認' ? EnProgress::SCREENING_RESULT_APPROVED : null;   // 審査結果
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
        $enProgress->desired_move_in_date = $regData['ru059'] ?? null;          // 賃貸物件内容入居希望日
        $enProgress->desired_contract_date = $regData['ru060'] ?? null;         // 賃貸物件内容契約希望日
        $enProgress->planned_payment_date = $regData['ru061'] ?? null;          // 賃貸物件内容初期費用入金予定日
        $enProgress->save();

        $enProgressIndividualApplicant = EnProgressIndividualApplicant::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressIndividualApplicant->en_progress_id = $enProgress->id;
        $enProgressIndividualApplicant->last_name = $regData['ru063'] ?? null;              // 申込者氏名（名字）
        $enProgressIndividualApplicant->first_name = $regData['ru064'] ?? null;             // 申込者氏名（名前）
        $enProgressIndividualApplicant->last_kana = $regData['ru065'] ?? null;              // 申込者氏名（名字カナ）
        $enProgressIndividualApplicant->first_kana = $regData['ru066'] ?? null;             // 申込者氏名（名前カナ）
        $enProgressIndividualApplicant->gender = $this->getGender($regData['ru067'] ?? null, EnProgressIndividualApplicant::class);     // 申込者性別
        $enProgressIndividualApplicant->birth_date = $regData['ru068'] ?? null;             // 申込者生年月日
        $enProgressIndividualApplicant->mobile_phone_number = $regData['ru070'] ?? null;    // 申込者携帯電話番号
        $enProgressIndividualApplicant->email = $regData['ru071'] ?? null;                  // 申込者メールアドレス
        $enProgressIndividualApplicant->phone_number = $regData['ru072'] ?? null;           // 申込者自宅電話番号
        $enProgressIndividualApplicant->postal_code = $regData['ru073'] ?? null;            // 申込者現住所（郵便番号）
        $enProgressIndividualApplicant->prefecture = $regData['ru074'] ?? null;             // 申込者現住所（都道府県）
        $enProgressIndividualApplicant->city = $regData['ru075'] ?? null;                   // 申込者現住所（市区町村）
        $enProgressIndividualApplicant->street = $regData['ru076'] ?? null;                 // 申込者現住所（番地・丁目）
        $enProgressIndividualApplicant->building = $regData['ru077'] ?? null;               // 申込者現住所（建物名・部屋番号）
        $enProgressIndividualApplicant->residence_type = $regData['ru078'] ?? null;         // 申込者住居種別
        $enProgressIndividualApplicant->residence_years = $regData['ru079'] ?? null;        // 申込者居住年数
        $enProgressIndividualApplicant->move_reason = $regData['ru080'] ?? null;            // 申込者転居理由
        $enProgressIndividualApplicant->occupation = $regData['ru081'] ?? null;             // お勤め先職業
        $enProgressIndividualApplicant->workplace_name = $regData['ru082'] ?? null;         // お勤め先勤務先/学校名
        $enProgressIndividualApplicant->workplace_kana = $regData['ru083'] ?? null;         // お勤め先勤務先/学校名（カナ）
        $enProgressIndividualApplicant->workplace_phone_number = $regData['ru084'] ?? null; // お勤め先勤務先電話番号
        $enProgressIndividualApplicant->workplace_postal_code = $regData['ru085'] ?? null;  // お勤め先勤務先所在地（郵便番号）
        $enProgressIndividualApplicant->workplace_prefecture = $regData['ru086'] ?? null;   // お勤め先勤務先所在地（都道府県）
        $enProgressIndividualApplicant->workplace_city = $regData['ru087'] ?? null;         // お勤め先勤務先所在地（市区町村）
        $enProgressIndividualApplicant->workplace_street = $regData['ru088'] ?? null;       // お勤め先勤務先所在地（番地・丁目）
        $enProgressIndividualApplicant->workplace_building = $regData['ru089'] ?? null;     // お勤め先勤務先所在地（建物名・部屋番号）
        $enProgressIndividualApplicant->industry = $regData['ru090'] ?? null;               // お勤め先業種
        $enProgressIndividualApplicant->established_date = $regData['ru091'] ?? null;       // お勤め先設立年月日
        $enProgressIndividualApplicant->capital = $regData['ru092'] ?? null;                // お勤め先資本金
        $enProgressIndividualApplicant->years_of_service = $regData['ru093'] ?? null;       // お勤め先勤続年数
        $enProgressIndividualApplicant->annual_income = $regData['ru094'] ?? null;          // お勤め先税込年収
        $enProgressIndividualApplicant->save();

        $enProgressOccupants = EnProgressOccupants::firstOrNew([
            'en_progress_id' => $enProgress->id,
            'occupant_seq' => 1
        ]);
        $enProgressOccupants->en_progress_id = $enProgress->id;
        $enProgressOccupants->last_name = $regData['ru095'] ?? null;                        // 入居者1氏名（名字）
        $enProgressOccupants->first_name = $regData['ru096'] ?? null;                       // 入居者1氏名（名前）
        $enProgressOccupants->last_kana = $regData['ru097'] ?? null;                        // 入居者1氏名（名字カナ）
        $enProgressOccupants->first_kana = $regData['ru098'] ?? null;                       // 入居者1氏名（名前カナ）
        $enProgressOccupants->gender = $this->getGender($regData['ru099'] ?? null, EnProgressOccupants::class);     // 入居者1性別
        $enProgressOccupants->relationship = $regData['ru100'] ?? null;                     // 入居者1続柄
        $enProgressOccupants->birth_date = $regData['ru101'] ?? null;                       // 入居者1生年月日
        $enProgressOccupants->mobile_phone_number = $regData['ru103'] ?? null;              // 入居者1携帯電話番号
        $enProgressOccupants->workplace_or_school_name = $regData['ru104'] ?? null;         // 入居者1勤務先/学校名
        $enProgressOccupants->workplace_or_school_kana = $regData['ru105'] ?? null;         // 入居者1勤務先/学校名（カナ）
        $enProgressOccupants->save();

        // 入居者2
        if ($regData['ru106'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 2
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru106'] ?? null;                        // 入居者2氏名（名字）
            $enProgressOccupants->first_name = $regData['ru107'] ?? null;                       // 入居者2氏名（名前）
            $enProgressOccupants->last_kana = $regData['ru108'] ?? null;                        // 入居者2氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru109'] ?? null;                       // 入居者2氏名（名前カナ）
            $enProgressOccupants->gender = $this->getGender($regData['ru110'] ?? null, EnProgressOccupants::class);     // 入居者2性別
            $enProgressOccupants->relationship = $regData['ru111'] ?? null;                     // 入居者2続柄
            $enProgressOccupants->birth_date = $regData['ru112'] ?? null;                       // 入居者2生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru114'] ?? null;              // 入居者2携帯電話番号
            $enProgressOccupants->workplace_or_school_name = $regData['ru115'] ?? null;         // 入居者2勤務先/学校名
            $enProgressOccupants->workplace_or_school_kana = $regData['ru116'] ?? null;         // 入居者2勤務先/学校名（カナ）
            $enProgressOccupants->save();
        }

        // 入居者3
        if ($regData['ru117'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 3
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru117'] ?? null;                        // 入居者3氏名（名字）
            $enProgressOccupants->first_name = $regData['ru118'] ?? null;                       // 入居者3氏名（名前）
            $enProgressOccupants->last_kana = $regData['ru119'] ?? null;                        // 入居者3氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru120'] ?? null;                       // 入居者3氏名（名前カナ）
            $enProgressOccupants->gender = $this->getGender($regData['ru121'] ?? null, EnProgressOccupants::class);     // 入居者3性別
            $enProgressOccupants->relationship = $regData['ru122'] ?? null;                     // 入居者3続柄
            $enProgressOccupants->birth_date = $regData['ru123'] ?? null;                       // 入居者3生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru125'] ?? null;              // 入居者3携帯電話番号
            $enProgressOccupants->workplace_or_school_name = $regData['ru126'] ?? null;         // 入居者3勤務先/学校名
            $enProgressOccupants->workplace_or_school_kana = $regData['ru127'] ?? null;         // 入居者3勤務先/学校名（カナ）
            $enProgressOccupants->save();
        }

        // 入居者4
        if ($regData['ru128'] ?? null) {
            $enProgressOccupants = EnProgressOccupants::firstOrNew([
                'en_progress_id' => $enProgress->id,
                'occupant_seq' => 4
            ]);
            $enProgressOccupants->en_progress_id = $enProgress->id;
            $enProgressOccupants->last_name = $regData['ru128'] ?? null;                        // 入居者4氏名（名字）
            $enProgressOccupants->first_name = $regData['ru129'] ?? null;                       // 入居者4氏名（名前）
            $enProgressOccupants->last_kana = $regData['ru130'] ?? null;                        // 入居者4氏名（名字カナ）
            $enProgressOccupants->first_kana = $regData['ru131'] ?? null;                       // 入居者4氏名（名前カナ）
            $enProgressOccupants->gender = $this->getGender($regData['ru132'] ?? null, EnProgressOccupants::class);     // 入居者4性別
            $enProgressOccupants->relationship = $regData['ru133'] ?? null;                     // 入居者4続柄
            $enProgressOccupants->birth_date = $regData['ru134'] ?? null;                       // 入居者4生年月日
            $enProgressOccupants->mobile_phone_number = $regData['ru136'] ?? null;              // 入居者4携帯電話番号
            $enProgressOccupants->workplace_or_school_name = $regData['ru137'] ?? null;         // 入居者4勤務先/学校名
            $enProgressOccupants->workplace_or_school_kana = $regData['ru138'] ?? null;         // 入居者4勤務先/学校名（カナ）
            $enProgressOccupants->save();
        }

        $enProgressEmergencyContact = EnProgressEmergencyContact::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressEmergencyContact->en_progress_id = $enProgress->id;
        $enProgressEmergencyContact->last_name = $regData['ru139'] ?? null;                         // 緊急連絡先氏名（名字）
        $enProgressEmergencyContact->first_name = $regData['ru140'] ?? null;                        // 緊急連絡先氏名（名前）
        $enProgressEmergencyContact->last_kana = $regData['ru141'] ?? null;                         // 緊急連絡先氏名（名字カナ）
        $enProgressEmergencyContact->first_kana = $regData['ru142'] ?? null;                        // 緊急連絡先氏名（名前カナ）
        $enProgressEmergencyContact->gender = $this->getGender($regData['ru143'] ?? null, EnProgressEmergencyContact::class);     // 緊急連絡先性別
        $enProgressEmergencyContact->birth_date = $regData['ru144'] ?? null;                        // 緊急連絡先生年月日
        $enProgressEmergencyContact->relationship = $regData['ru146'] ?? null;                      // 緊急連絡先続柄
        $enProgressEmergencyContact->mobile_phone_number = $regData['ru147'] ?? null;               // 緊急連絡先携帯電話番号
        $enProgressEmergencyContact->phone_number = $regData['ru148'] ?? null;                      // 緊急連絡先自宅電話番号
        $enProgressEmergencyContact->postal_code = $regData['ru149'] ?? null;                       // 緊急連絡先自宅住所（郵便番号）
        $enProgressEmergencyContact->prefecture = $regData['ru150'] ?? null;                        // 緊急連絡先自宅住所（都道府県）
        $enProgressEmergencyContact->city = $regData['ru151'] ?? null;                              // 緊急連絡先自宅住所（市区町村）
        $enProgressEmergencyContact->street = $regData['ru152'] ?? null;                            // 緊急連絡先自宅住所（番地・丁目）
        $enProgressEmergencyContact->building = $regData['ru153'] ?? null;                          // 緊急連絡先自宅住所（建物名・部屋番号）
        $enProgressEmergencyContact->workplace_or_school_name = $regData['ru154'] ?? null;          // 緊急連絡先勤務先名
        $enProgressEmergencyContact->workplace_or_school_kana = $regData['ru155'] ?? null;          // 緊急連絡先勤務先名（カナ）
        $enProgressEmergencyContact->save();
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
        return view('livewire.admin.import.personal-tenancy-application-import');
    }
}
