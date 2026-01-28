<?php
namespace App\Livewire\Admin\Import\Concerns;

use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use App\Models\KpiType;
use App\Models\Ksf;
use App\Models\Progress;
use App\Models\TeProgress;

trait SyncKsf {
    public function createKsfForLe($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->create($progress, KpiType::DEPARTMENT_LE);
    }

    public function syncKsfForLe($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->sync($progress, KpiType::DEPARTMENT_LE);
    }

    public function deleteKsfForLe($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->delete($progressId, KpiType::DEPARTMENT_LE);
    }

    public function createKsfForEn($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->create($progress, KpiType::DEPARTMENT_EN);
    }

    public function syncKsfForEn($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->sync($progress, KpiType::DEPARTMENT_EN);
    }

    public function deleteKsfForEn($progressId) {
        $progress = Progress::with([
                'investmentRoom',
                'investmentEmptyRoom',
            ])
            ->find($progressId);
        if (!$progress) return;

        $this->delete($progressId, KpiType::DEPARTMENT_EN);
    }

    public function createKsfForTe($progressId) {
        $progress = TeProgress::with([
                'responsible'
            ])
            ->find($progressId);
        if (!$progress) return;

        $identifiers = [];
        switch ($progress->category1_master_id) {
            case Category1Master::CLAIM_SOFT:
                $identifiers[] = 'soft_resolution';
                break;
            case Category1Master::CLAIM_HARD:
                $identifiers[] = 'own_repair_proposal';
                $identifiers[] = 'pc_repair_order';
                break;
            case Category1Master::INQUIRY:
            case Category1Master::CANCEL_INQUIRY:
                $identifiers[] = 'inquiry_resolution';

                if ($progress->responsible_id) {
                    if ($progress->responsible->isTe()) {
                        $identifiers[] = 'te_resolution';
                    }

                    if ($progress->responsible->isLe()) {
                        $identifiers[] = 'le_resolution';
                    }

                    if ($progress->responsible->isAc()) {
                        $identifiers[] = 'keiri_resolution';
                    }
                }
                break;
            default:
                // カテゴリ１が対象外の場合はデータを作成しない
        }

        if ($identifiers) {
            $this->create($progress, KpiType::DEPARTMENT_TE, $identifiers);
        }
    }

    public function syncKsfForTe($progressId) {
        if (!Ksf::where('progress_id', $progressId)->exists()) {
            $this->createKsfForTe($progressId);
        }

        $progress = TeProgress::find($progressId);

        if (!$progress) return;

        $this->sync($progress, KpiType::DEPARTMENT_TE);
    }

    public function deleteKsfForTe($progressId, $oldCategory1MasterId) {
        $progress = TeProgress::with([
                'responsible'
            ])
            ->find($progressId);
        if (!$progress) return;

        // カテゴリ１が変更されたら削除
        if ($progress->category1_master_id != $oldCategory1MasterId) {
            $this->delete($progressId, KpiType::DEPARTMENT_TE);
        }

        if ($progress->category1_master_id == Category1Master::INQUIRY || $progress->category1_master_id == Category1Master::CANCEL_INQUIRY) {
            $identifiers = [
                'te_resolution' => 'te_resolution',
                'le_resolution' => 'le_resolution',
                'keiri_resolution' => 'keiri_resolution',
            ];

            if ($progress->responsible && $progress->responsible->isTe()) {
                unset($identifiers['te_resolution']);
            }

            if ($progress->responsible && $progress->responsible->isLe()) {
                unset($identifiers['le_resolution']);
            }

            if ($progress->responsible && $progress->responsible->isAc()) {
                unset($identifiers['keiri_resolution']);
            }

            $this->delete($progressId, KpiType::DEPARTMENT_TE, $identifiers);
        }
    }

    private function create($progress, $department, $identifiers = []) {
        $kpiTypes = KpiType::where(
                'department', $department,
            )
            ->get();

        try {
            foreach ($kpiTypes as $kpiType) {
                // 識別子が指定されていたらその識別子以外は作成しない
                if ($identifiers && !in_array($kpiType->identifier, $identifiers)) {
                    continue;
                }

                // 基準日フィールドが設定されていないKPI種別は作成しない
                if (!$kpiType->base_field) {
                    continue;
                }

                // 作成データ取得
                $ksfData = $this->getKsfData($kpiType, $progress);

                // 基準日にデータが存在していなかったら作成しない
                if (!$ksfData['base_date']) {
                    continue;
                }

                // データがすでに存在していたら作成しない
                if (!Ksf::where('progress_id', $progress->id)
                        ->where('kpi_type_id', $kpiType->id)
                        ->exists()) {
                    Ksf::create($ksfData);
                }
            }
        }
        catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    private function sync($progress, $department) {
        $ksfs = Ksf::with('kpyType')
            ->where('progress_id', $progress->id)
            ->where('is_locked', false)
            ->whereHas('kpyType', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->get();

        if ($ksfs) {
            try {
                foreach ($ksfs as $ksf) {
                    $kpiType = KpiType::find($ksf->kpi_type_id);

                    // 更新データ取得
                    $ksfData = $this->getKsfData($kpiType, $progress);

                    if ($ksfData['base_date']) {
                        $ksf->fill($ksfData);
                        $ksf->save();
                    }
                    else {
                        // 基準日が空ならデータ削除
                        $ksf->delete();
                    }
                }
            }
            catch (\Exception $e) {
                throw $e;
            }
        }

        return true;
    }

    private function delete($progressId, $department, $identifiers = []) {
        $ksfs = Ksf::with('kpyType')
            ->where('progress_id', $progressId)
            ->whereHas('kpyType', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->get();

        foreach ($ksfs as $ksf) {
            // 識別子が指定されていたらその識別子以外は削除しない
            if ($identifiers && !in_array($ksf->kpyType?->identifier, $identifiers)) {
                continue;
            }

            $ksf->delete();
        }
    }

    private function isExists($progressId, $department) {
        return Ksf::where('progress_id', $progressId)->exists();
    }

    private function getKsfData($kpi_type, $progress) {
        $ksf_data = [
            'progress_id' => $progress->id,
            'investment_id' => $progress->investment_id,
            'investment_room_id' => $progress->investment_room_id,
            'department' => $kpi_type->department,
            'kpi_type_id' => $kpi_type->id,
            'base_date' => null,
            'user_id' => null,
            'completed_date' => null,
            'trading_company_id' => null,
        ];

// Log::debug('identifier = ' . $kpi_type->identifier);
        switch ($kpi_type->identifier) {
            case 'recruitment':             // 募集開始
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->le_responsible_id;
                $ksf_data['completed_date'] = $progress->marketing_kakumei_date;
                break;
            case 'long_proposal':           // 長期提案
                $ksf_data['base_date'] = $progress->investmentEmptyRoom->{$kpi_type->base_field} ?? '';
                $ksf_data['user_id'] = $progress->le_responsible_id;
                $ksf_data['completed_date'] = $progress->le_final_suggestion_date;
                break;
            case 'restoration_completed':   // 原復完工
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->le_responsible_id;
                $ksf_data['completed_date'] = $progress->kanko_jyushin_date;
                break;
            case 'own_examination':         // OWN審査
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->en_responsible_id;
                $ksf_data['completed_date'] = $progress->owner_shoudaku_date;
                break;
            case 'contract_deposit':        // 契約入金
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->en_responsible_id;
                $ksf_data['completed_date'] = $progress->keiyaku_nyukin_date;
                break;
            case 'contract_collection':     // 契約回収
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->en_responsible_id;
                $ksf_data['completed_date'] = $progress->keiyaku_collect_date;
                break;
            case 'own_repair_proposal':     // OWN修繕提案
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->responsible_id;
                $ksf_data['completed_date'] = $progress->own_suggestion_date;
                $ksf_data['trading_company_id'] = $progress->genpuku_gyousha_id;
            break;
            case 'pc_repair_order':         // PC修繕発注
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->responsible_id;
                $ksf_data['completed_date'] = $progress->pc_hachu_date;
                $ksf_data['trading_company_id'] = $progress->genpuku_gyousha_id;
                break;
            case 'soft_resolution':         // ソフト解決
            case 'inquiry_resolution':      // 問合せ解決
            case 'te_resolution':           // 問合せ解決（TE）
            case 'le_resolution':           // 問合せ解決（LE）
            case 'keiri_resolution':        // 問合せ解決（経理）
                $ksf_data['base_date'] = $progress->{$kpi_type->base_field};
                $ksf_data['user_id'] = $progress->responsible_id;
                $ksf_data['completed_date'] = $progress->complete_date;
                $ksf_data['trading_company_id'] = $progress->genpuku_gyousha_id;
                break;
        }

        return $ksf_data;
    }

}
