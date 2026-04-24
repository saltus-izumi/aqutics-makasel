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

class Step3 extends Component
{
    use WithFileUploads;
    use BuildsGeProgressMailReplacements;

    public $geProgress = null;
    public $completionMessage;

    public array $completionPhotoUploads = [];
    public array $completionPhotoFiles = [];
    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'completionMessage' => 'construction_ccompletion_message',
    ];
    protected function rules(): array
    {
        return [
            'completionMessage' => ['nullable', 'string'],
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
        $this->completionMessage = $geProgress?->construction_ccompletion_message;
        $this->componentId = $this->getId();
        $this->loadCompletionPhotoFiles();
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

    public function saveCompletionPhotoUploads(): void
    {
        if (!$this->geProgress) {
            return;
        }

        foreach ($this->completionPhotoUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$this->geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $this->geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_COMPLETION_PHOTO,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->completionPhotoUploads = [];
        $this->loadCompletionPhotoFiles();
    }

    public function sendConstructionCompletionMail(): void
    {
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

        if ($geProgress->construction_completion_date) {
            return;
        }

        $mailTemplate = MailTemplate::query()
            ->where('mail_kind', MailTemplate::MAIL_KIND_GE_PROGRESS_CONSTRUCTION_DONE)
            ->first();

        if (!$mailTemplate || (!$mailTemplate->subject && !$mailTemplate->body)) {
            Log::warning('工事完工メールテンプレートが存在しないため送信を中止しました。', [
                'ge_progress_id' => $this->geProgress->id,
                'mail_kind' => MailTemplate::MAIL_KIND_GE_PROGRESS_CONSTRUCTION_DONE,
            ]);
            return;
        }

        $tradingCompany = TradingCompany::query()
            ->find($geProgress->trading_company_id);

        $to = collect(
            preg_split('/[,\s;]+/', (string) config('mail.ge_progress_aqutics_mail_address', ''), -1, PREG_SPLIT_NO_EMPTY) ?: []
        )
            ->filter(fn ($mail) => is_string($mail) && filter_var(trim($mail), FILTER_VALIDATE_EMAIL))
            ->map(fn ($mail) => trim((string) $mail))
            ->unique()
            ->values()
            ->all();

        if (empty($to)) {
            Log::warning('GE_PROGRESS_AQUTICS_MAIL_ADDRESS に有効な送信先メールアドレスが存在しないため送信を中止しました。', [
                'ge_progress_id' => $this->geProgress->id,
            ]);
            return;
        }

        $replacements = $this->buildGeProgressMailReplacements($geProgress, $tradingCompany);
        $subject = strtr((string) ($mailTemplate->subject ?? ''), $replacements);
        $body = strtr((string) ($mailTemplate->body ?? ''), $replacements);

        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });

        $this->geProgress->construction_completion_date = today();
        $this->geProgress->save();

        $this->dispatch('geProgressUpdated', geProgressId: $this->geProgress->id);
    }

    public function removeCompletionPhotoFile($fileId): void
    {
        if (!$this->geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_COMPLETION_PHOTO)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadCompletionPhotoFiles();
    }

    public function reloadProgress($progressId = null)
    {
        if (!$this->geProgress) {
            return;
        }

        if ($progressId !== null && (int) $progressId !== (int) $this->geProgress->id) {
            return;
        }

        $this->geProgress = GeProgress::query()
            ->find($this->geProgress->id);
    }

    protected function loadCompletionPhotoFiles(): void
    {
        if (!$this->geProgress) {
            $this->completionPhotoFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $this->geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_COMPLETION_PHOTO)
            ->orderBy('id')
            ->get();

        $this->completionPhotoFiles = $files->map(function (GeProgressFile $file) {
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
        return view('livewire.admin.progress.ge.step3');
    }
}
