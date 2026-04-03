<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\Category1Master;
use App\Models\Category2Master;
use App\Models\Category3Master;
use App\Models\TeProgress;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step1 extends Component
{
    use WithFileUploads;

    public $teProgress = null;
    public $category1MasterOptions = [];
    public $category2MasterOptions = [];
    public $category3MasterOptions = [];
    public $tradingCompanyOptions = [];
    public array $step1Uploads = [];
    public array $step1Files = [];
    public string $componentId = '';

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];
    protected array $step1FieldConfig = [
        'category1_master_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'category2_master_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'category3_master_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'title' => ['rules' => ['nullable', 'string'], 'type' => 'string'],
        'trading_company_1_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'trading_company_2_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
        'trading_company_3_id' => ['rules' => ['nullable', 'integer'], 'type' => 'integer'],
    ];

    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;

        $this->category1MasterOptions = Category1Master::query()
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();

        $this->loadCategory2MasterOptions($teProgress->category1_master_id);
        $this->loadCategory3MasterOptions($teProgress->category2_master_id);

        $this->tradingCompanyOptions = TradingCompany::query()
            ->where('trading_status', TradingCompany::TRADING_STATUS_ENABLE)
            ->orderBy('id')
            ->pluck('name', 'id')
            ->toArray();

        $this->componentId = $this->getId();
    }

    public function saveFieldByName(string $fieldName, $value): void
    {
        if (!$this->teProgress || !array_key_exists($fieldName, $this->step1FieldConfig)) {
            return;
        }

        $validator = Validator::make(
            [$fieldName => $value],
            [$fieldName => $this->step1FieldConfig[$fieldName]['rules']]
        );

        if ($validator->fails()) {
            return;
        }

        $normalizedValue = $this->normalizeFieldValue($fieldName, $value);
        $this->teProgress->{$fieldName} = $normalizedValue;

        $this->syncCategoryDependents($fieldName);

        $this->teProgress->save();

        $this->dispatch('teProgressUpdated', teProgressId: $this->teProgress->id);
    }

    protected function normalizeFieldValue(string $fieldName, $value)
    {
        $type = $this->step1FieldConfig[$fieldName]['type'] ?? 'string';

        if ($value === '') {
            return null;
        }

        return match ($type) {
            'integer' => $this->normalizeInteger($value),
            default => $this->normalizeString($value),
        };
    }

    protected function normalizeInteger($value): ?int
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return null;
        }

        return (int) str_replace(',', '', $trimmed);
    }

    protected function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    protected function syncCategoryDependents(string $fieldName): void
    {
        if ($fieldName === 'category1_master_id') {
            $this->loadCategory2MasterOptions($this->teProgress->category1_master_id);

            if (!$this->optionContainsValue($this->teProgress->category2_master_id, $this->category2MasterOptions)) {
                $this->teProgress->category2_master_id = null;
            }

            $this->loadCategory3MasterOptions($this->teProgress->category2_master_id);

            if (!$this->optionContainsValue($this->teProgress->category3_master_id, $this->category3MasterOptions)) {
                $this->teProgress->category3_master_id = null;
            }

            $this->dispatchCategory2Options();
            $this->dispatchCategory3Options();

            return;
        }

        if ($fieldName === 'category2_master_id') {
            $this->loadCategory3MasterOptions($this->teProgress->category2_master_id);

            if (!$this->optionContainsValue($this->teProgress->category3_master_id, $this->category3MasterOptions)) {
                $this->teProgress->category3_master_id = null;
            }

            $this->dispatchCategory3Options();
        }
    }

    protected function optionContainsValue($value, array $options): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        $target = (string) $value;

        foreach (array_keys($options) as $optionKey) {
            if ((string) $optionKey === $target) {
                return true;
            }
        }

        return false;
    }

    protected function loadCategory2MasterOptions($category1MasterId): void
    {
        if ($category1MasterId === null || $category1MasterId === '') {
            $this->category2MasterOptions = [];
            return;
        }

        $this->category2MasterOptions = Category2Master::query()
            ->where('category1_master_id', $category1MasterId)
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    protected function loadCategory3MasterOptions($category2MasterId): void
    {
        if ($category2MasterId === null || $category2MasterId === '') {
            $this->category3MasterOptions = [];
            return;
        }

        $this->category3MasterOptions = Category3Master::query()
            ->where('category2_master_id', $category2MasterId)
            ->orderBy('id')
            ->pluck('item_name', 'id')
            ->toArray();
    }

    protected function dispatchCategory2Options(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'category2_master_id',
            options: $this->category2MasterOptions,
            value: $this->teProgress->category2_master_id === null ? '' : (string) $this->teProgress->category2_master_id,
        );
    }

    protected function dispatchCategory3Options(): void
    {
        $this->dispatch(
            'select-search-options',
            name: 'category3_master_id',
            options: $this->category3MasterOptions,
            value: $this->teProgress->category3_master_id === null ? '' : (string) $this->teProgress->category3_master_id,
        );
    }

    public function updateMoveOutReportDate(): void
    {
        if ($this->teProgress->move_out_report_date) {
            return;
        }

        $this->teProgress->move_out_report_date = now();
        $this->teProgress->save();
    }

    public function reloadProgress($teProgressId = null)
    {
        if (!$this->teProgress) {
            return;
        }

        if ($teProgressId !== null && (int) $teProgressId !== (int) $this->teProgress->id) {
            return;
        }

        $this->teProgress = TeProgress::query()
            ->find($this->teProgress->id);

        if (!$this->teProgress) {
            return;
        }

        $this->loadCategory2MasterOptions($this->teProgress->category1_master_id);
        $this->loadCategory3MasterOptions($this->teProgress->category2_master_id);
    }

    public function render()
    {
        return view('livewire.admin.progress.te.step1');
    }
}
