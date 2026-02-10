<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgressFile;
use App\Models\Progress;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Step6 extends Component
{
    use WithFileUploads;

    public $progress = null;
    public $completionMessage;

    public array $otherCompletionPhotoUploads = [];
    public array $otherCompletionPhotoFiles = [];
    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'completionMessage' => 'completion_message',
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

    public function mount($progress)
    {
        $this->progress = $progress;
        $this->completionMessage = $progress->geProgress?->completion_message;
        $this->componentId = $this->getId();
        $this->loadOtherCompletionPhotoFiles();
    }

    public function updated($propertyName, $value)
    {
        if (!array_key_exists($propertyName, $this->geProgressMap)) {
            return;
        }

        // null対策
        if (!$this->progress?->geProgress) {
            return;
        }

        $this->validateOnly($propertyName);

        $value = trim($value) ? trim($value) : null;

        $column = $this->geProgressMap[$propertyName];
        $this->progress->geProgress->{$column} = $value;
        $this->progress->geProgress->save();

        $this->dispatch('geProgressUpdated', progressId: $this->progress->id);
    }

    public function saveOtherCompletionPhotoUploads(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            return;
        }

        foreach ($this->otherCompletionPhotoUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/ge/{$geProgress->id}");

            GeProgressFile::create([
                'ge_progress_id' => $geProgress->id,
                'file_kind' => GeProgressFile::FILE_KIND_OTHER_COMPLETION_PHOTO,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->otherCompletionPhotoUploads = [];
        $this->loadOtherCompletionPhotoFiles();
    }

    public function removeOtherCompletionPhotoFile($fileId): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress || !$fileId) {
            return;
        }

        $file = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_OTHER_COMPLETION_PHOTO)
            ->where('id', $fileId)
            ->first();

        if (!$file) {
            return;
        }

        if ($file->file_path && Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();
        $this->loadOtherCompletionPhotoFiles();
    }

    public function reloadProgress($progressId = null)
    {
        if (!$this->progress) {
            return;
        }

        if ($progressId !== null && (int) $progressId !== (int) $this->progress->id) {
            return;
        }

        $this->progress = Progress::query()
            ->with('geProgress')
            ->find($this->progress->id);
    }

    protected function loadOtherCompletionPhotoFiles(): void
    {
        $geProgress = $this->progress?->geProgress;
        if (!$geProgress) {
            $this->otherCompletionPhotoFiles = [];
            return;
        }

        $files = GeProgressFile::query()
            ->where('ge_progress_id', $geProgress->id)
            ->where('file_kind', GeProgressFile::FILE_KIND_OTHER_COMPLETION_PHOTO)
            ->orderBy('id')
            ->get();

        $this->otherCompletionPhotoFiles = $files->map(function (GeProgressFile $file) {
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
        return view('livewire.admin.progress.ge.step6');
    }
}
