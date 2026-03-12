<?php

namespace App\Livewire\Admin\Import;

use App\Models\Broker;
use App\Models\CorporateTenancyApplicationLog;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
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

                if ($room) {
                    $regData['investment_id'] = $room->investment_id;
                    $regData['investment_room_id'] = $room->investment_room_id;
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

                if (($regData['ru035'] ?? null) === '承認' && $room) {
                    $this->upsertInvestmentRoomResident($room, $regData);
                }

                $broker = $this->upsertBroker($regData);
                if ($room && $broker) {
                    Investment::query()
                        ->where('id', $room->investment_id)
                        ->update(['broker_id' => $broker->id]);
                }
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
            'desired_move_in_date' => $row[58] ?? null,
            'desired_contract_date' => $row[59] ?? null,
            'initial_payment_due_date' => $row[60] ?? null,
            'tokio_marine_insurance_flag' => $row[61] ?? null,
            'tokio_marine_direct_insurance_flag' => $row[62] ?? null,
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
            'corporate_emergency_contact_work_phone' => $row[146] ?? null,
            'guarantor_last_name' => $row[147] ?? null,
            'guarantor_first_name' => $row[148] ?? null,
            'guarantor_last_name_kana' => $row[149] ?? null,
            'guarantor_first_name_kana' => $row[150] ?? null,
            'guarantor_gender' => $row[151] ?? null,
            'guarantor_birth_date' => $row[152] ?? null,
            'guarantor_age' => $row[153] ?? null,
            'guarantor_relationship' => $row[154] ?? null,
            'guarantor_mobile_phone' => $row[155] ?? null,
            'guarantor_home_phone' => $row[156] ?? null,
            'guarantor_zip' => $row[157] ?? null,
            'guarantor_prefecture' => $row[158] ?? null,
            'guarantor_city' => $row[159] ?? null,
            'guarantor_address' => $row[160] ?? null,
            'guarantor_building' => $row[161] ?? null,
            'guarantor_residence_type' => $row[162] ?? null,
            'guarantor_residence_years' => $row[163] ?? null,
            'guarantor_job' => $row[164] ?? null,
            'guarantor_company_name_kana' => $row[165] ?? null,
            'guarantor_company_phone' => $row[166] ?? null,
            'guarantor_company_zip' => $row[167] ?? null,
            'guarantor_company_prefecture' => $row[168] ?? null,
            'guarantor_company_city' => $row[169] ?? null,
            'guarantor_company_address' => $row[170] ?? null,
            'guarantor_company_building' => $row[171] ?? null,
            'guarantor_industry' => $row[172] ?? null,
            'guarantor_company_established_date' => $row[173] ?? null,
            'guarantor_company_capital' => $row[174] ?? null,
            'guarantor_annual_income' => $row[175] ?? null,
            'guarantor_years_employed' => $row[176] ?? null,
            'corporate_company_registry_document' => $row[177] ?? null,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.import.corporate-tenancy-application-import');
    }
}
