<?php

namespace App\Livewire\Admin\Import;

use App\Livewire\Admin\Import\Concerns\SyncKsf;
use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use App\Models\FortificationsProposal;
use App\Models\GeProgress;
use App\Models\Investment;
use App\Models\InvestmentRoom;
use App\Models\InvestmentRoomResident;
use App\Models\Onsite;
use App\Models\Procall;
use App\Models\Progress;
use App\Models\SuggestEmptyRoom;
use App\Models\SuggestEmptyRoomNewEquipment;
use App\Models\TeProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportProcall extends Component
{
    use WithFileUploads;
    use SyncKsf;

    public $procallFile = null;
    public $isUpdate = false;
    public $insertCount = null;
    public $updateCount = null;
    public $errorCount = null;
    public $errorMessages = [];

    protected $messages = [
        'procallFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        $this->validate([
            'procallFile' => ['required', 'file'],
        ]);

        $this->insertCount = 0;
        $this->updateCount = 0;
        $this->errorCount = 0;
        $this->errorMessages = [];


        DB::beginTransaction();

        try {
            $file = new \SplFileObject($this->procallFile->getRealPath(), 'r');
            $rowCount = 1;

            while (! $file->eof()) {
                $row = $file->fgetcsv();
                if ($row === [null] || $row === false) {
                    continue;
                }

                // ヘッダー行読み飛ばし
                if ($rowCount === 1) {
                    $rowCount++;
                    continue;
                }

                // $row = mb_convert_encoding($row, 'UTF-8', 'SJIS-win');
                $result = $this->insert($row);
                if ($result !== true) {
                    $this->errorMessages[] = "{$rowCount}行目：" . $result;
                }
                $rowCount++;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e; // 必要なら再スロー
        }

        if ($this->errorCount == 0) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        $this->reset('procallFile');
    }

    public function render()
    {
        return view('livewire.admin.import.import-procall');
    }

    protected function insert($record)
    {
        $investmentName = trim($record[17]);                                       // 建物名
        $roomNumber = mb_convert_kana(str_replace('号室', '', $record[18] ), 'n'); // 部屋番号

        $procallExists = Procall::where('pr001', $record[0])->exists();

        if ($procallExists && $this->isUpdate == false) {
            $this->errorCount++;
            return "すでに登録済みデータです。（案件ID：{$record[0]}）";
        } elseif ($procallExists && $this->isUpdate) {
            return $this->update($record);
        }

        try {
            // CSVログ保存
            $this->insertProcall($record);

            // 物件情報検索
            $inventment = Investment::getByInvestmentNameForProcall($investmentName);
            if (!$inventment) {
                $this->errorCount++;
                return "物件が見つかりません（物件名：{$investmentName}）";
            }

            if ($record[18] !== '') {
                $inventmentRoom = InvestmentRoom::getByInvestmentRoomNumberForProcall($inventment->id, $roomNumber);
                if (empty($inventmentRoom)) {
                    $this->errorCount++;
                    return "部屋が見つかりません（物件：{$investmentName}({$inventment->id})、部屋：{$roomNumber}）";
                }

                // 現時点の入居者情報を取得
                $investmentRoomResident = InvestmentRoomResident::query()
                    ->where('investment_room_uid', $inventmentRoom->id)
                    ->first();
            }
            else {
                $inventmentRoom = new InvestmentRoom();
                $investmentRoomResident = null;
            }

            if ($record[18] == '') {
                $record[18] = '共用部';
            }

            if ($record[23] == "解約受付") {
                $leavingDate = new Carbon($record[32]);     // 連絡日時
                $incidentDate = new Carbon($record[2]);     // 案件発生日

                // すでに取り込み済みか確認
                if (!Progress::where('procall_deal_id', $record[0])->exists()) {
                    $progress = new Progress([
                        'investment_id' => $inventment->id,
                        'investment_room_id' => $inventmentRoom->investment_room_id ?? 0,
                        'investment_room_uid' => $inventmentRoom?->id ?? null,
                        'contractor_no' => $investmentRoomResident?->contractor_no ?? null,
                        'investment_name' => $record[17],
                        'investment_room_name' => $record[18],
                        'procall_deal_id' => $record[0],
                        'status_ge' => 0,
                        'status_le' => 0,
                        'status_en' => 0,
                        'ge_application_date' => $incidentDate,
                        'taikyo_uketuke_date' => $leavingDate,
                        'kaiyaku_date' => $leavingDate,
                        'last_import_date' => now(),
                    ]);

                    // 原復会社を自動設定
                    $progress->genpuku_gyousha_id = $inventment->restoration_company_id ?? null;

                    // 担当者を自動設定
                    $progress->le_responsible_id = $inventment->le_staff_id ?? null;
                    $progress->genpuku_responsible_id = $inventment->le_staff_id ?? null;
                    $progress->en_responsible_id = $inventment->en_staff_id ?? null;
                    $progress->en_responsible_2_id = $inventment->en_staff_id ?? null;
                    $progress->save();

                    // GeProgresses作成
                    GeProgress::create([
                        'progress_id' => $progress->id,
                        'trading_company_id' => $inventment->restoration_company_id,
                    ]);


                    $this->insertCount++;

                    // fortifications_proposalsにデータ追加
                    $fortificationsProposal = FortificationsProposal::create([
                        'investment_id' => $inventment->id,
                        'investment_room_id' => $inventmentRoom->investment_room_id ?? 0,
                        'progress_id' => $progress->id,
                    ]);

                    $progress->fortifications_proposal_id = $fortificationsProposal->id;
                    $progress->save();

                    // suggest_empty_roomsにデータ追加
                    $suggestEmptyRoom = SuggestEmptyRoom::create([
                        'investment_id' => $inventment->id,
                        'investment_room_id' => $inventmentRoom->investment_room_id ?? 0,
                        'investment_empty_room_id' => $inventmentRoom->investment_room_id ?? 0,
                        'progress_id' => $progress->id,
                        'type' => 1,
                    ]);

                    SuggestEmptyRoomNewEquipment::createEquipments($suggestEmptyRoom->id, $inventment->id, $inventmentRoom->investment_room_id ?? 0);

                    // ksfデータ作成
                    $this->createKsfForLe($progress->id);
                    $this->createKsfForEn($progress->id);

                    Onsite::regit($inventmentRoom,
                        [Onsite::ONSITE_REQEST_KIND_ORIGINAL_SUBTRACTION, Onsite::ONSITE_REQEST_KIND_COMPLETION_SHOOTING],
                        $leavingDate, $leavingDate, Auth::user());
                }
            } else {
                $typeId = 0;
                $leavingDate = new Carbon($record[32]);
                if ($record[28] == '完了') {
                    $pcStatus = 1;
                }
                else {
                    $pcStatus = 0;
                }
                $category1Id = 0;
                $category2Id = 0;
                $category3Id = 0;
                $category1Name = $record[23];
                $category2Name = $record[24];
                $category3Name = $record[25];

                if ($category1Name != '') {
                    $category1 = Category1Master::where('item_name', $category1Name)
                        ->first();
                    if ($category1) {
                        $category1Id = $category1->id;
                    }
                    else {
                        $category1 = Category1Master::create([
                            'item_name' => $category1Name
                        ]);
                        $category1Id = $category1->id;
                    }
                }
                if ($category2Name != '' && $category2Name != 'ー') {
                    $category2 = Category2Master::where('category1_master_id', $category1Id)
                        ->where('item_name', $category2Name)
                        ->first();
                    if ($category2) {
                        $category2Id = $category2->id;
                    }
                    else {
                        $category2 = Category2Master::create([
                            'item_name' => $category2Name,
                            'category1_master_id' => $category1Id
                        ]);
                        $category2Id = $category2->id;
                    }
                }
                if ($category3Name != '' && $category3Name != 'ー') {
                    $category3 = Category3Master::where('category2_master_id', $category2Id)
                        ->where('item_name', $category3Name)
                        ->first();
                    if ($category3) {
                        $category3Id = $category3->id;
                    }
                    else{
                        $category3 = Category3Master::create([
                            'item_name' => $category3Name,
                            'category2_master_id' => $category2Id
                        ]);
                        $category3Id = $category3->id;
                    }
                }
                if (!TeProgress::where('procall_deal_id', $record[0])->exists()) {
                    $progress = new TeProgress([
                        'investment_id' => $inventment->id,
                        'investment_room_id' => $inventmentRoom->investment_room_id,
                        'contractor_no' => $investmentRoomResident?->contractor_no ?? null,
                        'investment_name' => $record[17],
                        'investment_room_name' => $record[18],
                        'procall_deal_id' => $record[0],
                        'procall_case_no' => $record[1],    // 案件番号
                        'status' => 0,
                        'target_name' => $record[21], // 契約者
                        'lead_wire_type' => 1,
                        'title' => $record[22], // 案件タイトルを概要に入れる
                        'status_remarks' => $record[27], // ステータス（社内対応履歴）
                        'category1' => $category1Name,
                        'category2' => $category2Name,
                        'category3' => $category3Name,
                        'category1_master_id' => $category1Id,
                        'category2_master_id' => $category2Id,
                        'category3_master_id' => $category3Id,
                        'pc_status_remarks' => $record[35], // 連絡内容
                        'pc_status' => $pcStatus,
                        'nyuuden_date' => $record[32], // 連絡日時を入電日
                        'type_id' => $typeId,
                        'type_name' => $record[9], // カテゴリ1階層を案件分類に
                        'complete_date' => $this->nullIfEmpty($record[31]),
                        'last_import_date' => now(),
                    ]);
                    $progress->save();
                    $this->insertCount++;

                    // fortifications_proposalsにデータ追加
                    $fortificationsProposal = FortificationsProposal::create([
                        'investment_id' => $inventment->id,
                        'investment_room_id' => $inventmentRoom->investment_room_id ?? 0,
                        'te_progress_id' => $progress->id,
                    ]);

                    $progress->fortifications_proposal_id = $fortificationsProposal->id;
                    $progress->te_id = $progress->te_id ? $progress->te_id : $progress->id;
                    $progress->save();

                    // ksfデータ作成(入電日)
                    $this->createKsfForTe($progress->id);
                }
            }
        } catch (\Exception $e) {
            $this->errorCount++;
            return $e->getMessage();
        }

        return true;
    }

    protected function update($record) {
        $progress = Progress::where('procall_deal_id', $record[0])
            ->where(function ($query) {
                return $query->where('last_import_date', '<', now()->startOfDay())
                    ->orWhere('last_import_kind', Progress::LAST_IMPORT_KIND_UPDATE);
            })
            ->first();
        if ($progress) {
            $progress->last_import_date = now();
            $progress->last_import_kind = Progress::LAST_IMPORT_KIND_UPDATE;
            $progress->complete_date = null;
            $progress->save();

            $this->updateCount++;

            // ksfデータ更新
            $this->syncKsfForLe($progress->id);
            $this->syncKsfForEn($progress->id);
        }
        else {
            $teProgress = TeProgress::where('procall_case_no', $record[1])
                ->where(function ($query) {
                    return $query->where('last_import_date', '<', now()->startOfDay())
                        ->orWhere('last_import_kind', TeProgress::LAST_IMPORT_KIND_UPDATE)
                        ->orWhere('is_pm_created', true);
                })
                ->first();

            if ($teProgress) {
                $category1Id = 0;
                $category2Id = 0;
                $category3Id = 0;
                $category1Name = $record[23];
                $category2Name = $record[24];
                $category3Name = $record[25];
                if ($category1Name != '') {
                    $category1 = Category1Master::where('item_name', $category1Name)
                        ->first();
                    if ($category1) {
                        $category1Id = $category1->id;
                    }
                    else {
                        $category1 = Category1Master::create([
                            'item_name' => $category1Name
                        ]);
                        $category1Id = $category1->id;
                    }
                }
                if ($category2Name != '' && $category2Name != 'ー') {
                    $category2 = Category2Master::where('category1_master_id', $category1Id)
                        ->where('item_name', $category2Name)
                        ->first();
                    if ($category2) {
                        $category2Id = $category2->id;
                    }
                    else {
                        $category2 = Category2Master::create([
                            'item_name' => $category2Name,
                            'category1_master_id' => $category1Id
                        ]);
                        $category2Id = $category2->id;
                    }
                }
                if ($category3Name != '' && $category3Name != 'ー') {
                    $category3 = Category3Master::where('category2_master_id', $category2Id)
                        ->where('item_name', $category3Name)
                        ->first();
                    if ($category3) {
                        $category3Id = $category3->id;
                    }
                    else{
                        $category3 = Category3Master::create([
                            'item_name' => $category3Name,
                            'category2_master_id' => $category2Id
                        ]);
                        $category3Id = $category3->id;
                    }
                }

                $teProgress->procall_deal_id = $record[0];
                $teProgress->title = $record[22];
                $teProgress->category1 = $category1Name;
                $teProgress->category2 = $category2Name;
                $teProgress->category3 = $category3Name;
                $teProgress->category1_master_id = $category1Id;
                $teProgress->category2_master_id = $category2Id;
                $teProgress->category3_master_id = $category3Id;
                $teProgress->nyuuden_date = str_replace('/', '-', $record[32] ?? '');
                $teProgress->pc_status_remarks = $record[35];
// dd($te_progress);
                $teProgress->last_import_date = now();
                $teProgress->last_import_kind = TeProgress::LAST_IMPORT_KIND_UPDATE;
                if ($record[28] != '完了') {
                    $teProgress->complete_date = null;
                }
                $teProgress->save();

                $this->updateCount++;

                $this->syncKsfForTe($teProgress->id);
            }
        }

        return true;
    }

    protected function insertProcall($record) {
        $procalls = Procall::create([
            'pr001' =>  $record[0],
            'pr002' =>  $record[1],
            'pr003' =>  $this->nullIfEmpty($record[2]),
            'pr004' =>  $record[3],
            'pr005' =>  $record[4],
            'pr006' =>  $record[5],
            'pr007' =>  $record[6],
            'pr008' =>  $record[7],
            'pr009' =>  $record[8],
            'pr010' =>  $record[9],
            'pr011' =>  $record[10],
            'pr012' =>  $record[11],
            'pr013' =>  $record[12],
            'pr014' =>  $record[13],
            'pr015' =>  $record[14],
            'pr016' =>  $record[15],
            'pr017' =>  $record[16],
            'pr018' =>  $record[17],
            'pr019' =>  $record[18],
            'pr020' =>  $record[19],
            'pr021' =>  $record[20],
            'pr022' =>  $record[21],
            'pr023' =>  $record[22],
            'pr024' =>  $record[23],
            'pr025' =>  $record[24],
            'pr026' =>  $record[25],
            'pr027' =>  $record[26],
            'pr028' =>  $record[27],
            'pr029' =>  $record[28],
            'pr030' =>  $record[29],
            'pr031' =>  $record[30],
            'pr032' =>  $this->nullIfEmpty($record[31]),
            'pr033' =>  $this->nullIfEmpty($record[32]),
            'pr034' =>  $record[33],
            'pr035' =>  $record[34],
            'pr036' =>  $record[35],
            'pr037' =>  $record[36],
        ]);

        return $procalls;
    }

    protected function nullIfEmpty($value)
    {
        return $value === '' ? null : $value;
    }
}
