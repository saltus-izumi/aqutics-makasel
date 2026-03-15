<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use App\Models\GuaranteeCompany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContractTerms extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $latestGeProgress = null;
    public $guaranteeCompanyOptions = [];

    public $securityDepositAmount = null;
    public $proratedRentAmount = null;
    public $penaltyForfeitureAmount = null;
    public $inspectionRequestMessage = null;
    public $isStep1Confirmed = false;
    public array $step1Uploads = [];
    public array $step1Files = [];
    public string $componentId = '';

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $enProgressMap = [
        'securityDepositAmount' => 'security_deposit_amount',
        'proratedRentAmount' => 'prorated_rent_amount',
        'penaltyForfeitureAmount' => 'penalty_forfeiture_amount',
        'inspectionRequestMessage' => 'inspection_request_message',
        'isStep1Confirmed' => 'is_step1_confirmed',
    ];
    protected function rules(): array
    {
        return [
            'securityDepositAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'proratedRentAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'penaltyForfeitureAmount' => ['nullable', 'regex:/^[+-]?(?:\d+|\d{1,3}(,\d{3})+)$/'],
            'inspectionRequestMessage' => ['nullable', 'string'],
            'isStep1Confirmed' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'securityDepositAmount.regex' => '敷金預託等は半角数字で入力してください。',
            'proratedRentAmount.regex' => '日割り家賃は半角数字で入力してください。',
            'penaltyForfeitureAmount.regex' => '違約金（償却）は半角数字で入力してください。',
        ];
    }

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->latestGeProgress = $enProgress->progress?->latestGeProgress;
        $this->guaranteeCompanyOptions = GuaranteeCompany::query()
            ->orderBy('id')
            ->pluck('company_name', 'id')
            ->toArray();

        $this->securityDepositAmount = $enProgress?->security_deposit_amount;
        $this->proratedRentAmount = $enProgress?->prorated_rent_amount;
        $this->penaltyForfeitureAmount = $enProgress?->penalty_forfeiture_amount;
        $this->inspectionRequestMessage = $enProgress?->inspection_request_message;
        $this->isStep1Confirmed = $enProgress?->is_step1_confirmed;
        $this->componentId = $this->getId();
        $this->loadStep1Files();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->enProgressMap)) {
            return;
        }

        // null対策
        if (!$this->enProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        if ($propertyName === 'isStep1Confirmed') {
            $value = (bool) $value;
        } else {
            switch($propertyName) {
                case 'securityDepositAmount':
                case 'proratedRentAmount':
                case 'penaltyForfeitureAmount':
                    $value = str_replace(',', '', (string) $value);
                    break;
            }

            if (is_string($value)) {
                $value = trim($value) !== '' ? trim($value) : null;
            }
        }

        $column = $this->enProgressMap[$propertyName];
        $this->enProgress->{$column} = $value;
        $this->enProgress->save();

        $this->dispatch('enProgressUpdated', enProgressId: $this->enProgress->id);
    }

    public function updateMoveOutReportDate(): void
    {
        if ($this->enProgress->move_out_report_date) {
            return;
        }

        $this->enProgress->move_out_report_date = now();
        $this->enProgress->save();
    }

    public function saveStep1Uploads(): void
    {
        foreach ($this->step1Uploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$this->enProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $this->enProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_STEP1,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->step1Uploads = [];
        $this->loadStep1Files();
    }

    public function removeStep1File($fileId): void
    {
        if (!$this->enProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $this->enProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadStep1Files();
    }

    protected function loadStep1Files(): void
    {
        if (!$this->enProgress) {
            $this->step1Files = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $this->enProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->orderBy('id')
            ->get();

        $this->step1Files = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['enProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function getFileMimeType(GeProgressFile $file): string
    {
        if (!$file->file_path || !Storage::disk('local')->exists($file->file_path)) {
            return '';
        }

        $fullPath = Storage::disk('local')->path($file->file_path);
        return mime_content_type($fullPath) ?: '';
    }

    public function reloadProgress($enProgressId = null)
    {
        if (!$this->enProgress) {
            return;
        }

        if ($enProgressId !== null && (int) $enProgressId !== (int) $this->enProgress->id) {
            return;
        }

        $this->enProgress = GeProgress::query()
            ->find($this->enProgress->id);
    }


    public function render()
    {
        return view('livewire.admin.progress.en.contract-terms');
    }
}
