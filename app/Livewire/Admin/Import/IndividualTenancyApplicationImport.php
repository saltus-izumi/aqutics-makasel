<?php

namespace App\Livewire\Admin\Import;

use App\Models\Broker;
use App\Models\BrokerInvestment;
use App\Models\EnProgress;
use App\Models\EnProgressEmergencyContact;
use App\Models\EnProgressGuarantor;
use App\Models\EnProgressIndividualApplicant;
use App\Models\EnProgressOccupant;
use App\Models\GuaranteeCompany;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\IndividualTenancyApplicationLog;
use App\Models\Progress;
use App\Models\ReactionPersonal;
use App\Models\SummaryPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndividualTenancyApplicationImport extends Component
{
    use WithFileUploads;

    public $individualTenancyApplicationFile = null;

    public ?int $readCount = null;
    public ?int $insertResidentCount = null;
    public ?int $updateResidentCount = null;
    public ?int $errorCount = null;
    public array $errorMessages = [];

    protected $messages = [
        'individualTenancyApplicationFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        $this->validate([
            'individualTenancyApplicationFile' => ['required', 'file'],
        ]);

        $this->readCount = 0;
        $this->insertResidentCount = 0;
        $this->updateResidentCount = 0;
        $this->errorCount = 0;
        $this->errorMessages = [];

        $originalName = $this->individualTenancyApplicationFile->getClientOriginalName();
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
            SummaryPeriod::query()->firstOrCreate(
                ['start_date' => $startDate->toDateString()],
                ['end_date' => $endDate->toDateString()]
            );

            ReactionPersonal::query()
                ->whereDate('sampling_date', $samplingDate->toDateString())
                ->delete();

            $csv = new \SplFileObject($this->individualTenancyApplicationFile->getRealPath(), 'r');
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

        $this->reset('individualTenancyApplicationFile');
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

        if ($broker) {
            $this->fillModelFromRegDataIfTargetEmpty($broker, [
                'itanji_id' => 'ru012',
                'broker_name' => 'ru013',
                'broker_name_kana' => 'ru014',
                'broker_mobile_tel' => 'ru015',
                'broker_tantou_name' => 'ru016',
                'broker_mail' => 'ru017',
                'broker_fax' => 'ru018',
                'broker_tel' => 'ru019',
                'broker_zip' => 'ru020',
                'broker_address' => 'ru021',
            ], $regData);

            if ($this->isFieldEmpty($broker->area_id) && array_key_exists('ru021', $regData)) {
                $broker->area_id = $this->resolveAreaId((string) $regData['ru021']);
            }

            $broker->save();
            return $broker;
        }

        return Broker::query()->create([
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
        ]);
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

        IndividualTenancyApplicationLog::create([
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
            'guarantor_workplace_name' => $row[173] ?? null,
            'guarantor_workplace_name_kana' => $row[174] ?? null,
            'guarantor_workplace_phone' => $row[175] ?? null,
            'guarantor_workplace_zip' => $row[176] ?? null,
            'guarantor_workplace_prefecture' => $row[177] ?? null,
            'guarantor_workplace_city' => $row[178] ?? null,
            'guarantor_workplace_address' => $row[179] ?? null,
            'guarantor_workplace_building' => $row[180] ?? null,
            'guarantor_workplace_industry' => $row[181] ?? null,
            'guarantor_workplace_established_date' => $row[182] ?? null,
            'guarantor_workplace_capital' => $row[183] ?? null,
            'guarantor_annual_income' => $row[184] ?? null,
            'guarantor_years_employed' => $row[185] ?? null,
            'applicant_id_document_front' => $row[186] ?? null,
            'applicant_id_document_back' => $row[187] ?? null,
            'applicant_income_certificate' => $row[188] ?? null,
            'applicant_additional_document_1' => $row[189] ?? null,
            'created_user_id' => $row[190] ?? null,
            'user_created_at' => $row[191] ?? null,
            'updated_user_id' => $row[192] ?? null,
            'user_updated_at' => $row[193] ?? null,
            'deleted_user_id' => $row[194] ?? null,
            'user_deleted_at' => $row[195] ?? null,
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
        $this->fillModelFromRegDataIfTargetEmpty($progress, [
            'en_responsible_id' => 'en_staff_id',
            'keiyaku_shiki_date' => 'ru060',
            'mousikomi_date' => 'ru031',
        ], $regData);
        $progress->save();

        $enProgress = EnProgress::firstOrNew(['application_id' => $regData['ru001'] ?? null]);
        $enProgress->progress_id = $progress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgress, [
            'responsible_user_id' => 'en_staff_id',
            'broker_id' => 'broker_id',
            'application_date' => 'ru031',
            'guarantee_company_plan' => 'ru034',
            'approval_guarantee_company_plan' => 'ru034',
            'screening_result' => 'ru035',
            'approval_number' => 'ru036',
            'priority_order' => 'ru039',
            'rent_fee' => 'ru049',
            'common_service_fee' => 'ru050',
            'water_fee' => 'ru051',
            'neighborhood_fee' => 'ru052',
            'transfer_fee' => 'ru053',
            'parking_fee' => 'ru054',
            'other_fixed_fee' => 'ru055',
            'deposit_fee' => 'ru057',
            'security_deposit_fee' => 'ru058',
            'desired_move_in_date' => 'ru059',
            'desired_contract_date' => 'ru060',
            'planned_payment_date' => 'ru061',
        ], $regData, [
            'screening_result' => static fn ($value) => $value === '承認' ? EnProgress::SCREENING_RESULT_APPROVED : null,
        ]);

        $this->fillIfTargetEmpty($enProgress, 'application_id', $regData['ru001'] ?? null); // 申込ID
        $this->fillIfTargetEmpty($enProgress, 'applicant_type', EnProgress::APPLICANT_TYPE_INDIVIDUAL); // 申込人種別

        if (array_key_exists('ru055', $regData) && !$this->isFieldEmpty($regData['ru055'])) {
            $enProgress->anshin_support_flag = true;
        }

        if (
            $this->isFieldEmpty($enProgress->guarantee_company_id) &&
            array_key_exists('ru033', $regData)
        ) {
            // 保証会社データ取得
            $guaranteeCompany = GuaranteeCompany::firstOrCreate([
                'company_name' => $regData['ru033'],
            ]);
            $enProgress->guarantee_company_id = $guaranteeCompany->id; // 保証会社ID
        }

        if (
            $this->isFieldEmpty($enProgress->approval_guarantee_company_id) &&
            array_key_exists('ru033', $regData)
        ) {
            // 保証会社データ取得
            $guaranteeCompany = GuaranteeCompany::firstOrCreate([
                'company_name' => $regData['ru033'],
            ]);
            $enProgress->approval_guarantee_company_id = $guaranteeCompany->id; // 保証会社ID
        }

        if (
            !$this->isFieldEmpty($enProgress->application_date) &&
            $this->isFieldEmpty($enProgress->application_date_state)
        ) {
            $enProgress->application_date_state = 1;
        }

        $enProgress->resetNextAction();
        $enProgress->save();

        $enProgressIndividualApplicant = EnProgressIndividualApplicant::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressIndividualApplicant->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressIndividualApplicant, [
            'last_name' => 'ru063',
            'first_name' => 'ru064',
            'last_kana' => 'ru065',
            'first_kana' => 'ru066',
            'gender' => 'ru067',
            'birth_date' => 'ru068',
            'mobile_phone_number' => 'ru070',
            'email' => 'ru071',
            'phone_number' => 'ru072',
            'postal_code' => 'ru073',
            'prefecture' => 'ru074',
            'city' => 'ru075',
            'street' => 'ru076',
            'building' => 'ru077',
            'residence_type' => 'ru078',
            'residence_years' => 'ru079',
            'move_reason' => 'ru080',
            'occupation' => 'ru081',
            'workplace_name' => 'ru082',
            'workplace_kana' => 'ru083',
            'workplace_phone_number' => 'ru084',
            'workplace_postal_code' => 'ru085',
            'workplace_prefecture' => 'ru086',
            'workplace_city' => 'ru087',
            'workplace_street' => 'ru088',
            'workplace_building' => 'ru089',
            'industry' => 'ru090',
            'established_date' => 'ru091',
            'capital' => 'ru092',
            'years_of_service' => 'ru093',
            'annual_income' => 'ru094',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressIndividualApplicant::class),
        ]);
        $enProgressIndividualApplicant->save();

        $this->upsertOccupant($enProgress->id, 1, [
            'last_name' => 'ru095',
            'first_name' => 'ru096',
            'last_kana' => 'ru097',
            'first_kana' => 'ru098',
            'gender' => 'ru099',
            'relationship' => 'ru100',
            'birth_date' => 'ru101',
            'mobile_phone_number' => 'ru103',
            'workplace_or_school_name' => 'ru104',
            'workplace_or_school_kana' => 'ru105',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressOccupant::class),
        ]);

        // 入居者2
        if ($regData['ru106'] ?? null) {
            $this->upsertOccupant($enProgress->id, 2, [
                'last_name' => 'ru106',
                'first_name' => 'ru107',
                'last_kana' => 'ru108',
                'first_kana' => 'ru109',
                'gender' => 'ru110',
                'relationship' => 'ru111',
                'birth_date' => 'ru112',
                'mobile_phone_number' => 'ru114',
                'workplace_or_school_name' => 'ru115',
                'workplace_or_school_kana' => 'ru116',
            ], $regData, [
                'gender' => fn ($value) => $this->getGender($value, EnProgressOccupant::class),
            ]);
        }

        // 入居者3
        if ($regData['ru117'] ?? null) {
            $this->upsertOccupant($enProgress->id, 3, [
                'last_name' => 'ru117',
                'first_name' => 'ru118',
                'last_kana' => 'ru119',
                'first_kana' => 'ru120',
                'gender' => 'ru121',
                'relationship' => 'ru122',
                'birth_date' => 'ru123',
                'mobile_phone_number' => 'ru125',
                'workplace_or_school_name' => 'ru126',
                'workplace_or_school_kana' => 'ru127',
            ], $regData, [
                'gender' => fn ($value) => $this->getGender($value, EnProgressOccupant::class),
            ]);
        }

        // 入居者4
        if ($regData['ru128'] ?? null) {
            $this->upsertOccupant($enProgress->id, 4, [
                'last_name' => 'ru128',
                'first_name' => 'ru129',
                'last_kana' => 'ru130',
                'first_kana' => 'ru131',
                'gender' => 'ru132',
                'relationship' => 'ru133',
                'birth_date' => 'ru134',
                'mobile_phone_number' => 'ru136',
                'workplace_or_school_name' => 'ru137',
                'workplace_or_school_kana' => 'ru138',
            ], $regData, [
                'gender' => fn ($value) => $this->getGender($value, EnProgressOccupant::class),
            ]);
        }

        $enProgressEmergencyContact = EnProgressEmergencyContact::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressEmergencyContact->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressEmergencyContact, [
            'last_name' => 'ru139',
            'first_name' => 'ru140',
            'last_kana' => 'ru141',
            'first_kana' => 'ru142',
            'gender' => 'ru143',
            'birth_date' => 'ru144',
            'relationship' => 'ru146',
            'mobile_phone_number' => 'ru147',
            'phone_number' => 'ru148',
            'postal_code' => 'ru149',
            'prefecture' => 'ru150',
            'city' => 'ru151',
            'street' => 'ru152',
            'building' => 'ru153',
            'workplace_or_school_name' => 'ru154',
            'workplace_or_school_kana' => 'ru155',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressEmergencyContact::class),
        ]);
        $enProgressEmergencyContact->save();


        $enProgressGuarantor = EnProgressGuarantor::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressGuarantor->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressGuarantor, [
            'last_name' => 'ru156',
            'first_name' => 'ru157',
            'last_kana' => 'ru158',
            'first_kana' => 'ru159',
            'gender' => 'ru160',
            'birth_date' => 'ru161',
            'relationship' => 'ru163',
            'mobile_phone_number' => 'ru164',
            'phone_number' => 'ru165',
            'postal_code' => 'ru166',
            'prefecture' => 'ru167',
            'city' => 'ru168',
            'street' => 'ru169',
            'building' => 'ru170',
            'residence_type' => 'ru171',
            'residence_years' => 'ru172',
            'occupation' => 'ru173',
            'workplace_name' => 'ru174',
            'workplace_kana' => 'ru175',
            'workplace_phone_number' => 'ru176',
            'workplace_postal_code' => 'ru177',
            'workplace_prefecture' => 'ru178',
            'workplace_city' => 'ru179',
            'workplace_street' => 'ru180',
            'workplace_building' => 'ru181',
            'industry' => 'ru182',
            'established_date' => 'ru183',
            'capital' => 'ru184',
            'annual_income' => 'ru185',
            'years_of_service' => 'ru186',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressGuarantor::class),
        ]);
        $enProgressGuarantor->save();
    }

    /**
     * @param array<string, string> $fieldMap
     * @param array<string, mixed> $regData
     * @param array<string, callable> $transforms
     */
    protected function upsertOccupant(
        int $enProgressId,
        int $occupantSeq,
        array $fieldMap,
        array $regData,
        array $transforms = []
    ): void {
        $enProgressOccupants = EnProgressOccupant::firstOrNew([
            'en_progress_id' => $enProgressId,
            'occupant_seq' => $occupantSeq,
        ]);
        $enProgressOccupants->en_progress_id = $enProgressId;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressOccupants, $fieldMap, $regData, $transforms);
        $enProgressOccupants->save();
    }

    protected function fillIfTargetEmpty(Model $model, string $attribute, mixed $value): void
    {
        if ($this->isFieldEmpty($value)) {
            return;
        }

        if (!$this->isFieldEmpty($model->getAttribute($attribute))) {
            return;
        }

        $model->setAttribute($attribute, $value);
    }

    /**
     * @param array<string, mixed> $regData
     */
    protected function fillFromRegDataIfTargetEmpty(
        Model $model,
        string $attribute,
        array $regData,
        string $sourceKey,
        ?callable $transform = null
    ): void {
        if (!array_key_exists($sourceKey, $regData)) {
            return;
        }

        $value = $regData[$sourceKey];
        if ($transform !== null) {
            $value = $transform($value);
        }

        $this->fillIfTargetEmpty($model, $attribute, $value);
    }

    /**
     * @param array<string, string> $fieldMap
     * @param array<string, mixed> $regData
     * @param array<string, callable> $transforms
     */
    protected function fillModelFromRegDataIfTargetEmpty(
        Model $model,
        array $fieldMap,
        array $regData,
        array $transforms = []
    ): void {
        foreach ($fieldMap as $attribute => $sourceKey) {
            $this->fillFromRegDataIfTargetEmpty(
                $model,
                $attribute,
                $regData,
                $sourceKey,
                $transforms[$attribute] ?? null
            );
        }
    }

    protected function isFieldEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
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
        return view('livewire.admin.import.individual-tenancy-application-import');
    }
}
