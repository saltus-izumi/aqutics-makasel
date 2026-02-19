<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step1 extends Component
{
    use WithFileUploads;

    public $geProgress = null;
    public $securityDepositAmount = null;
    public $proratedRentAmount = null;
    public $penaltyForfeitureAmount = null;
    public $inspectionRequestMessage = null;
    public $isStep1Confirmed = false;
    public array $step1Uploads = [];
    public array $step1Files = [];
    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
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

    public function mount($geProgress)
    {
        $this->geProgress = $geProgress;
        $this->securityDepositAmount = $geProgress?->security_deposit_amount;
        $this->proratedRentAmount = $geProgress?->prorated_rent_amount;
        $this->penaltyForfeitureAmount = $geProgress?->penalty_forfeiture_amount;
        $this->inspectionRequestMessage = $geProgress?->inspection_request_message;
        $this->isStep1Confirmed = $geProgress?->is_step1_confirmed;
        $this->componentId = $this->getId();
        $this->loadStep1Files();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->geProgressMap)) {
            return;
        }

        // null対策
        if (!$this->geProgress) {
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

        $column = $this->geProgressMap[$propertyName];
        $this->geProgress->{$column} = $value;
        $this->geProgress->save();

        $this->dispatch('geProgressUpdated', geProgressId: $this->geProgress->id);
    }

    public function updateMoveOutReportDate(): void
    {
        if ($this->geProgress->move_out_report_date) {
            return;
        }

        $this->geProgress->move_out_report_date = now();
        $this->geProgress->save();
    }

    public function saveStep1Uploads(): void
    {
        foreach ($this->step1Uploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$this->geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $this->geProgress->id,
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
        if (!$this->geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
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
        if (!$this->geProgress) {
            $this->step1Files = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_STEP1)
            ->orderBy('id')
            ->get();

        $this->step1Files = $files->map(function (GeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.ge.preview', ['geProgressFileId' => $file->id]),
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

    public function reloadProgress($geProgressId = null)
    {
        if (!$this->geProgress) {
            return;
        }

        if ($geProgressId !== null && (int) $geProgressId !== (int) $this->geProgress->id) {
            return;
        }

        $this->geProgress = GeProgress::query()
            ->find($this->geProgress->id);
    }


    public function render()
    {
        return view('livewire.admin.progress.ge.step1');
    }
}
