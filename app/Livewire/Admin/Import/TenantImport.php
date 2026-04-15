<?php

namespace App\Livewire\Admin\Import;

use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\InvestmentRoomResidentHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class TenantImport extends Component
{
    use WithFileUploads;

    private const CONTRACT_TYPES = [
        1 => '一般借家契約',
        2 => '普通建物賃貸借契約',
        3 => '定期借家契約',
    ];

    public $tenantFile = null;

    public ?int $readCount = null;
    public ?int $insertRoomCount = null;
    public ?int $insertResidentCount = null;
    public ?int $updateResidentCount = null;
    public ?int $errorCount = null;
    public array $errorMessages = [];

    protected $messages = [
        'tenantFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        try {
            $this->validate([
                'tenantFile' => ['required', 'file'],
            ]);

            $this->readCount = 0;
            $this->insertRoomCount = 0;
            $this->insertResidentCount = 0;
            $this->updateResidentCount = 0;
            $this->errorCount = 0;
            $this->errorMessages = [];

            DB::beginTransaction();

            try {
                $csv = new \SplFileObject($this->tenantFile->getRealPath(), 'r');
                // CP932で0x5Cを含む文字(例: 彌)の誤エスケープを防ぐため、escapeは無効化する
                $csv->setCsvControl(',', '"', '');
                $rowNo = 0;

                while (! $csv->eof()) {
                    $row = $csv->fgetcsv();
                    if ($row === false || $row === [null]) {
                        continue;
                    }

                    $rowNo++;
                    if ($rowNo === 1) {
                        continue;
                    }

                    $record = $this->convertEncoding($row);
                    if (($record[2] ?? '') === '' || ($record[0] ?? '') === '') {
                        continue;
                    }

                    $this->readCount++;

                    $investmentLabel = trim((string) ($record[1] ?? ''));
                    $investmentId = (string) ($record[0] ?? '');
                    $roomNumber = trim((string) ($record[2] ?? ''));

                    $investment = $this->findInvestment($investmentId);
                    if (!$investment) {
                        $this->errorCount++;
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s (%s) に該当する物件情報がありません。',
                            $rowNo,
                            $investmentLabel,
                            $investmentId
                        );
                        continue;
                    }

                    $room = $this->findRoom($investment, $roomNumber);
                    if (!$room) {
                        $this->errorCount++;
                        $this->errorMessages[] = sprintf(
                            '%d行目: %s (%s) %s に該当する部屋情報がありません。',
                            $rowNo,
                            $investmentLabel,
                            $investmentId,
                            $roomNumber
                        );
                        continue;
                    }

                    try {
                        $this->upsertRow($record, $room);
                    } catch (\Throwable $e) {
                        report($e);
                        $this->errorCount++;
                        $this->errorMessages[] = sprintf(
                            '%d行目: 取り込みに失敗しました。(%s)',
                            $rowNo,
                            $e->getMessage()
                        );
                    }
                }

                if ($this->errorCount === 0) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);
                $this->errorCount++;
                $this->errorMessages[] = '取り込み処理中にエラーが発生しました。(' . $e->getMessage() . ')';
            }

            $this->reset('tenantFile');
        } finally {
            $this->dispatch('close-tenant-import-loading-modal');
        }
    }

    public function render()
    {
        return view('livewire.admin.import.tenant-import');
    }

    protected function findInvestment(string $investmentId): ?Investment
    {
        $id = (int) $investmentId;
        if ($id <= 0) {
            return null;
        }

        return Investment::query()->find($id);
    }

    protected function findRoom(Investment $investment, string $roomNumber): ?InvestmentRoom
    {
        if ($roomNumber === '') {
            return null;
        }

        return InvestmentRoom::query()
            ->where('investment_id', $investment->id)
            ->where('investment_room_number', $roomNumber)
            ->first();
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
     * @param array<int, mixed> $record
     */
    protected function upsertRow(array $record, InvestmentRoom $room): void
    {
        $this->assignIfNotEmpty($room, 'money', $record[6] ?? null);
        $this->assignIfNotEmpty($room, 'sikikin', $record[14] ?? null);
        $this->assignIfNotEmpty($room, 'hosyokin', $record[15] ?? null);
        $this->assignIfNotEmpty($room, 'kyoeki', $record[7] ?? null);
        $this->assignIfNotEmpty($room, 'area_size', $record[3] ?? null);
        $this->assignIfNotEmpty($room, 'room_type', $record[4] ?? null);
        $this->assignIfNotEmpty($room, 'facing', $record[5] ?? null);

        InvestmentRoom::withoutEvents(function () use ($room): void {
            $room->save();
        });

        $resident = InvestmentRoomResident::query()
            ->where('investment_id', $room->investment_id)
            ->where('investment_room_id', $room->investment_room_id)
            ->first();

        if (!$resident) {
            $resident = new InvestmentRoomResident([
                'investment_id' => $room->investment_id,
                'investment_room_uid' => $room->id,
                'investment_room_id' => $room->investment_room_id,
            ]);
            $this->insertResidentCount++;
        } else {
            $this->updateResidentCount++;
            if (empty($resident->investment_room_uid)) {
                $resident->investment_room_uid = $room->id;
            }
        }

        $resident->rent_amount = $this->normalizeCell($record[6] ?? null);
        $resident->common_fee = $this->normalizeCell($record[7] ?? null);
        $resident->ansin_support = $this->normalizeCell($record[8] ?? null);
        $resident->fixed_water_fee = $this->normalizeCell($record[9] ?? null);
        $resident->fixed_utility_fee = $this->normalizeCell($record[10] ?? null);
        $resident->neighborhood_fee = $this->normalizeCell($record[11] ?? null);
        $resident->bank_transfer_fee = $this->normalizeCell($record[12] ?? null);
        $resident->move_out_cleaning_fee = $this->normalizeCell($record[13] ?? null);
        $resident->security_deposit = $this->normalizeCell($record[14] ?? null);
        $resident->guarantee_deposit = $this->normalizeCell($record[15] ?? null);
        $resident->renewal_fee = $this->normalizeCell($record[16] ?? null);
        $resident->renewal_administration_fee = $this->normalizeCell($record[17] ?? null);
        $resident->recontract_fee = $this->normalizeCell($record[18] ?? null);
        $resident->fire_insurance_fee = $this->normalizeCell($record[19] ?? null);
        $resident->first_contract_date = $this->normalizeDateCell($record[20] ?? null);
        $resident->contract_start_date = $this->normalizeDateCell($record[21] ?? null);
        $resident->contract_end_date = $this->normalizeDateCell($record[22] ?? null);
        $resident->contractor_name = $this->normalizeCell($record[23] ?? null);
        $resident->gender_id = $this->toGenderId($record[24] ?? null);
        $resident->age = $this->normalizeCell($record[25] ?? null);
        $resident->guarantor_name = $this->normalizeCell($record[26] ?? null);
        $resident->workplace = $this->normalizeCell($record[27] ?? null);
        $resident->cancellation_date = $this->normalizeDateCell($record[28] ?? null);
        $resident->insurance_company = $this->normalizeCell($record[29] ?? null);
        $resident->tel = $this->normalizeCell($record[30] ?? null);
        $resident->contractor_no = $this->normalizeCell($record[31] ?? null);

        $annualIncome = (int) ($record[32] ?? 0);
        $resident->annual_income = $annualIncome > 0 ? $annualIncome : 0;

        $contractTypeText = $this->normalizeCell($record[33] ?? null);
        $contractType = array_search($contractTypeText, self::CONTRACT_TYPES, true);
        $resident->contract_type = $contractType === false ? null : $contractType;

        $resident->save();

        if ($resident->contractor_no === null || $resident->contractor_no === '') {
            return;
        }

        $historyQuery = InvestmentRoomResidentHistory::query()
            ->where('investment_id', $resident->investment_id)
            ->where('investment_room_id', $resident->investment_room_id);
        if ($resident->contractor_no === null || $resident->contractor_no === '') {
            $historyQuery->whereNull('contractor_no');
        } else {
            $historyQuery->where('contractor_no', $resident->contractor_no);
        }
        $history = $historyQuery->first();
        if (!$history) {
            $history = new InvestmentRoomResidentHistory();
        }
        $history->timestamps = false;

        $history->investment_id = $resident->investment_id;
        $history->investment_room_uid = $resident->investment_room_uid;
        $history->investment_room_id = $resident->investment_room_id;
        $history->rent_amount = $resident->rent_amount;
        $history->common_fee = $resident->common_fee;
        $history->ansin_support = $resident->ansin_support;
        $history->fixed_water_fee = $resident->fixed_water_fee;
        $history->fixed_utility_fee = $resident->fixed_utility_fee;
        $history->neighborhood_fee = $resident->neighborhood_fee;
        $history->bank_transfer_fee = $resident->bank_transfer_fee;
        $history->move_out_cleaning_fee = $resident->move_out_cleaning_fee;
        $history->security_deposit = $resident->security_deposit;
        $history->guarantee_deposit = $resident->guarantee_deposit;
        $history->renewal_fee = $resident->renewal_fee;
        $history->renewal_administration_fee = $resident->renewal_administration_fee;
        $history->recontract_fee = $resident->recontract_fee;
        $history->fire_insurance_fee = $resident->fire_insurance_fee;
        $history->first_contract_date = $resident->first_contract_date;
        $history->contract_start_date = $resident->contract_start_date;
        $history->contract_end_date = $resident->contract_end_date;
        $history->contractor_name = $resident->contractor_name;
        $history->gender_id = $resident->gender_id;
        $history->age = $resident->age;
        $history->guarantor_name = $resident->guarantor_name;
        $history->workplace = $resident->workplace;
        $history->cancellation_date = $resident->cancellation_date;
        $history->insurance_company = $resident->insurance_company;
        $history->tel = $resident->tel;
        $history->contractor_no = $resident->contractor_no;
        $history->annual_income = $resident->annual_income;
        $history->contract_type = $resident->contract_type;

        InvestmentRoomResidentHistory::withoutEvents(function () use ($history): void {
            $history->save();
        });
    }

    protected function assignIfNotEmpty(Model $model, string $field, mixed $value): void
    {
        $normalized = $this->normalizeCell($value);
        if ($normalized === null) {
            return;
        }

        $model->{$field} = $normalized;
    }

    protected function normalizeCell(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return null;
            }
        }

        return $value;
    }

    protected function normalizeDateCell(mixed $value): ?string
    {
        $normalized = $this->normalizeCell($value);
        if ($normalized === null) {
            return null;
        }

        return str_replace('/', '-', (string) $normalized);
    }

    protected function toGenderId(mixed $value): ?int
    {
        $gender = $this->normalizeCell($value);
        if ($gender === '男性') {
            return 1;
        }
        if ($gender === '女性') {
            return 2;
        }

        return null;
    }
}
