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
use Illuminate\Database\Eloquent\Model;
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
            'desired_move_in_date' => 'ru060',
            'desired_contract_date' => 'ru061',
            'planned_payment_date' => 'ru062',
        ], $regData, [
            'screening_result' => static fn ($value) => $value === '承認' ? EnProgress::SCREENING_RESULT_APPROVED : null,
        ]);

        $this->fillIfTargetEmpty($enProgress, 'application_id', $regData['ru001'] ?? null); // 申込ID
        $this->fillIfTargetEmpty($enProgress, 'applicant_type', EnProgress::APPLICANT_TYPE_CORPORATE); // 申込人種別

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

        $enProgressCorporateApplicant = EnProgressCorporateApplicant::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressCorporateApplicant->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressCorporateApplicant, [
            'company_name' => 'ru064',
            'company_kana' => 'ru065',
            'head_office_postal_code' => 'ru066',
            'head_office_prefecture' => 'ru067',
            'head_office_city' => 'ru068',
            'head_office_street' => 'ru069',
            'head_office_building' => 'ru070',
            'head_office_phone_number' => 'ru071',
            'head_office_fax_number' => 'ru072',
            'email' => 'ru073',
            'industry' => 'ru074',
            'capital' => 'ru075',
            'number_of_employees' => 'ru076',
            'established_date' => 'ru077',
            'representative_last_name' => 'ru078',
            'representative_first_name' => 'ru079',
            'representative_last_kana' => 'ru080',
            'representative_first_kana' => 'ru081',
            'representative_mobile_phone_number' => 'ru082',
            'representative_postal_code' => 'ru083',
            'representative_prefecture' => 'ru084',
            'representative_city' => 'ru085',
            'representative_street' => 'ru086',
            'representative_building' => 'ru087',
            'contact_last_name' => 'ru088',
            'contact_first_name' => 'ru089',
            'contact_last_kana' => 'ru090',
            'contact_first_kana' => 'ru091',
            'contact_department' => 'ru092',
            'contact_phone_number' => 'ru093',
        ], $regData);
        $enProgressCorporateApplicant->save();

        $this->upsertOccupant($enProgress->id, 1, [
            'last_name' => 'ru094',
            'first_name' => 'ru095',
            'last_kana' => 'ru096',
            'first_kana' => 'ru097',
            'relationship' => 'ru098',
            'birth_date' => 'ru099',
            'mobile_phone_number' => 'ru101',
            'annual_income' => 'ru102',
        ], $regData);

        // 入居者2
        if ($regData['ru103'] ?? null) {
            $this->upsertOccupant($enProgress->id, 2, [
                'last_name' => 'ru103',
                'first_name' => 'ru104',
                'last_kana' => 'rru105',
                'first_kana' => 'ru106',
                'relationship' => 'ru107',
                'birth_date' => 'ru108',
                'mobile_phone_number' => 'ru110',
                'annual_income' => 'ru111',
            ], $regData);
        }

        // 入居者3
        if ($regData['ru112'] ?? null) {
            $this->upsertOccupant($enProgress->id, 3, [
                'last_name' => 'ru112',
                'first_name' => 'ru113',
                'last_kana' => 'ru114',
                'first_kana' => 'ru115',
                'relationship' => 'ru116',
                'birth_date' => 'ru117',
                'mobile_phone_number' => 'ru119',
                'annual_income' => 'ru120',
            ], $regData);
        }

        // 入居者4
        if ($regData['ru121'] ?? null) {
            $this->upsertOccupant($enProgress->id, 4, [
                'last_name' => 'ru121',
                'first_name' => 'ru122',
                'last_kana' => 'ru123',
                'first_kana' => 'ru124',
                'relationship' => 'ru125',
                'birth_date' => 'ru126',
                'mobile_phone_number' => 'ru128',
                'annual_income' => 'ru129',
            ], $regData);
        }

        $enProgressEmergencyContact = EnProgressEmergencyContact::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressEmergencyContact->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressEmergencyContact, [
            'last_name' => 'ru130',
            'first_name' => 'ru131',
            'last_kana' => 'ru132',
            'first_kana' => 'ru133',
            'gender' => 'ru134',
            'birth_date' => 'ru135',
            'relationship' => 'ru137',
            'mobile_phone_number' => 'ru138',
            'phone_number' => 'ru139',
            'postal_code' => 'ru140',
            'prefecture' => 'ru141',
            'city' => 'ru142',
            'street' => 'ru143',
            'building' => 'ru144',
            'workplace_or_school_name' => 'ru145',
            'workplace_or_school_kana' => 'ru146',
            'workplace_or_school_phone_number' => 'ru147',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressEmergencyContact::class),
        ]);
        $enProgressEmergencyContact->save();

        $enProgressGuarantor = EnProgressGuarantor::firstOrNew(['en_progress_id' => $enProgress->id]);
        $enProgressGuarantor->en_progress_id = $enProgress->id;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressGuarantor, [
            'last_name' => 'ru148',
            'first_name' => 'ru149',
            'last_kana' => 'ru150',
            'first_kana' => 'ru151',
            'gender' => 'ru152',
            'relationship' => 'ru153',
            'birth_date' => 'ru154',
            'mobile_phone_number' => 'ru156',
            'phone_number' => 'ru157',
            'postal_code' => 'ru158',
            'prefecture' => 'ru159',
            'city' => 'ru160',
            'street' => 'ru161',
            'building' => 'ru162',
            'residence_type' => 'ru163',
            'occupation' => 'ru164',
            'workplace_name' => 'ru165',
            'workplace_kana' => 'ru166',
            'workplace_phone_number' => 'ru167',
            'workplace_postal_code' => 'ru168',
            'workplace_prefecture' => 'ru169',
            'workplace_city' => 'ru170',
            'workplace_street' => 'ru171',
            'workplace_building' => 'ru172',
            'industry' => 'ru173',
            'capital' => 'ru174',
            'number_of_employees' => 'ru175',
            'established_date' => 'ru176',
            'annual_income' => 'ru177',
            'years_of_service' => 'ru178',
        ], $regData, [
            'gender' => fn ($value) => $this->getGender($value, EnProgressGuarantor::class),
        ]);
        $enProgressGuarantor->save();
    }

    /**
     * @param array<string, string> $fieldMap
     * @param array<string, mixed> $regData
     */
    protected function upsertOccupant(int $enProgressId, int $occupantSeq, array $fieldMap, array $regData): void
    {
        $enProgressOccupants = EnProgressOccupants::firstOrNew([
            'en_progress_id' => $enProgressId,
            'occupant_seq' => $occupantSeq,
        ]);
        $enProgressOccupants->en_progress_id = $enProgressId;
        $this->fillModelFromRegDataIfTargetEmpty($enProgressOccupants, $fieldMap, $regData);
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
        return view('livewire.admin.import.corporate-tenancy-application-import');
    }
}
