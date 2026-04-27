<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Livewire\Admin\Progress\Ge\Concerns\BuildsGeProgressMailReplacements;
use App\Models\GeProgress;
use App\Models\GeProgressFile;
use App\Models\MailTemplate;
use App\Models\TradingCompany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step7 extends Component
{
    use WithFileUploads;
    use BuildsGeProgressMailReplacements;

    public $geProgress = null;
    public $executorToRestorationCompanyMessage;

    public array $purchaseOrderUploads = [];
    public array $purchaseOrderFiles = [];
    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'executorToRestorationCompanyMessage' => 'executor_to_restoration_company_message',
    ];
    protected function rules(): array
    {
        return [
            'executorToRestorationCompanyMessage' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
        ];
    }

    public function mount($geProgress)
    {
        $this->geProgress = $geProgress;
        $this->executorToRestorationCompanyMessage = $geProgress?->executor_to_restoration_company_message;
        $this->componentId = $this->getId();
        $this->loadPurchaseOrderFiles();
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

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->geProgress->{$column} = $value;
        $this->geProgress->save();

        $this->dispatch('geProgressUpdated', geProgressId: $this->geProgress->id);
    }

    public function placeOrderToRestorationCompany(): void
    {
        $this->resetErrorBag('mailSend');

        $geProgress = GeProgress::query()
            ->with([
                'progress',
                'progress.investment',
                'progress.investmentRoom',
            ])
            ->find($this->geProgress->id);

        if (!$geProgress) {
            return;
        }

        $this->geProgress = $geProgress;

        if ($geProgress->ordered_date) {
            return;
        }

        $mailTemplate = MailTemplate::query()
            ->where('mail_kind', MailTemplate::MAIL_KIND_GE_PROGRESS_ORDER_PLACED)
            ->first();

        if (!$mailTemplate || (!$mailTemplate->subject && !$mailTemplate->body)) {
            $warningMessage = '原復会社発注メールテンプレートが存在しないため送信を中止しました。';
            Log::warning($warningMessage, [
                'ge_progress_id' => $this->geProgress->id,
                'mail_kind' => MailTemplate::MAIL_KIND_GE_PROGRESS_ORDER_PLACED,
            ]);
            $this->addError('mailSend', $warningMessage);
            return;
        }

        $tradingCompany = TradingCompany::query()
            ->find($geProgress->trading_company_id);

        $to = collect([
            $tradingCompany?->mail,
        ])
            ->filter(fn ($mail) => is_string($mail) && filter_var(trim($mail), FILTER_VALIDATE_EMAIL))
            ->map(fn ($mail) => trim((string) $mail))
            ->unique()
            ->values()
            ->all();

        if (empty($to)) {
            $warningMessage = '原復業者の送信先メールアドレスが存在しないため送信を中止しました。';
            Log::warning($warningMessage, [
                'ge_progress_id' => $this->geProgress->id,
                'trading_company_id' => $geProgress->trading_company_id,
            ]);
            $this->addError('mailSend', $warningMessage);
            return;
        }

        $replacements = $this->buildGeProgressMailReplacements($geProgress, $tradingCompany);
        $subject = strtr((string) ($mailTemplate->subject ?? ''), $replacements);
        $body = strtr((string) ($mailTemplate->body ?? ''), $replacements);

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });

        $this->geProgress->ordered_date = today();
        $this->geProgress->ordered_date_state = 1;
        $this->geProgress->save();

        $this->dispatch('geProgressUpdated', geProgressId: $this->geProgress->id);
    }

    public function savePurchaseOrderUploads(): void
    {
        if (!$this->geProgress) {
            return;
        }

        foreach ($this->purchaseOrderUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$this->geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $this->geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_PURCHASE_ORDER,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->purchaseOrderUploads = [];
        $this->loadPurchaseOrderFiles();
    }

    public function removePurchaseOrderFile($fileId): void
    {
        if (!$this->geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_PURCHASE_ORDER)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadPurchaseOrderFiles();
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

    protected function loadPurchaseOrderFiles(): void
    {
        if (!$this->geProgress) {
            $this->purchaseOrderFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_PURCHASE_ORDER)
            ->orderBy('id')
            ->get();

        $this->purchaseOrderFiles = $files->map(function (GeProgressFile $file) {
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

    public function render()
    {
        return view('livewire.admin.progress.ge.step7');
    }
}
