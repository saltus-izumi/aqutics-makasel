<?php

namespace App\Livewire\Admin\Import;

use App\Models\Broker;
use App\Models\BrokerInvestment;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\ReactionPersonal;
use App\Models\SummaryPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class PersonalTenancyApplicationImport extends Component
{
    use WithFileUploads;

    public $personalTenancyApplicationFile = null;

    public ?int $insertCount = null;
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

        $this->insertCount = 0;
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
            SummaryPeriod::query()->firstOrCreate(
                ['start_date' => $startDate->toDateString()],
                ['end_date' => $endDate->toDateString()]
            );

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
                $regData = $this->mapCsvRow($record);
                if ($regData === []) {
                    continue;
                }

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

                ReactionPersonal::query()->create($regData);
                $this->insertCount++;

                if (($regData['ru035'] ?? null) === '承認' && $room) {
                    $this->upsertInvestmentRoomResident($room, $regData);
                }

                $broker = $this->upsertBroker($regData);
                if ($room && $broker) {
                    BrokerInvestment::query()->firstOrCreate([
                        'broker_id' => $broker->id,
                        'investment_id' => $room->investment_id,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            $this->errorCount = ($this->errorCount ?? 0) + 1;
            $this->errorMessages[] = '取り込み処理中にエラーが発生しました。';
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
            ->where('investment_id', $room->investment_id)
            ->where('investment_room_id', $room->investment_room_id)
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
            'investment_room_id' => $room->investment_room_id,
            'contractor_name' => ($regData['ru062'] ?? '') . '　' . ($regData['ru063'] ?? ''),
            'gender_id' => $genderId,
            'age' => $regData['ru068'] ?? null,
            'attribute_id' => $attributeId,
            'workplace' => $regData['ru080'] ?? null,
            'annual_income' => $regData['ru092'] ?? null,
        ];

        if (isset($regData['ru154'], $regData['ru155'])) {
            $payload['guarantor_name'] = $regData['ru154'] . '　' . $regData['ru155'];
        }

        if ($resident) {
            $resident->fill($payload);
            $resident->save();
            return;
        }

        InvestmentRoomResident::query()->create($payload);
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

    public function render()
    {
        return view('livewire.admin.import.personal-tenancy-application-import');
    }
}
