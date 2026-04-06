<?php

namespace App\Livewire\Admin\Progress\Te;

use App\Models\TeProgress;
use App\Models\TeProgressFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HardStep5 extends Component
{
    use WithFileUploads;

    public $teProgress = null;

    public array $completionPhotoUploads = [];
    public array $completionPhotoFiles = [];

    protected $listeners = ['teProgressUpdated' => 'reloadProgress'];

    public function mount($teProgress)
    {
        $this->teProgress = $teProgress;
        $this->loadCompletionPhotoFiles();
    }

    public function saveCompletionPhotoUploads(): void
    {
        if (!$this->teProgress) {
            return;
        }

        foreach ($this->completionPhotoUploads as $file) {
            $original = $file->getClientOriginalName();
            $path = $file->store("progress/te/{$this->teProgress->id}");

            TeProgressFile::create([
                'te_progress_id' => $this->teProgress->id,
                'file_kind' => TeProgressFile::FILE_KIND_COMPLETION_PHOTO,
                'file_name' => $original,
                'file_path' => $path,
                'upload_at' => now(),
            ]);
        }

        $this->completionPhotoUploads = [];
        $this->loadCompletionPhotoFiles();
    }

    public function removeCompletionPhotoFile($fileId): void
    {
        if (!$this->teProgress || !$fileId) {
            return;
        }

        $file = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_COMPLETION_PHOTO)
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
        $this->loadCompletionPhotoFiles();
    }

    protected function loadCompletionPhotoFiles(): void
    {
        if (!$this->teProgress) {
            $this->completionPhotoFiles = [];
            return;
        }

        $files = TeProgressFile::query()
            ->where('te_progress_id', $this->teProgress->id)
            ->where('file_kind', TeProgressFile::FILE_KIND_COMPLETION_PHOTO)
            ->orderBy('id')
            ->get();

        $this->completionPhotoFiles = $files->map(function (TeProgressFile $file) {
            return [
                'id' => $file->id,
                'file_name' => $file->file_name ?? '',
                'url' => route('admin.progress.te.preview', ['teProgressFileId' => $file->id]),
                'file_path' => $file->file_path ?? '',
                'mime_type' => $this->getFileMimeType($file),
            ];
        })->all();
    }

    protected function getFileMimeType(TeProgressFile $file): string
    {
        if (!$file->file_path || !Storage::disk('local')->exists($file->file_path)) {
            return '';
        }

        $fullPath = Storage::disk('local')->path($file->file_path);
        return mime_content_type($fullPath) ?: '';
    }

    public function render()
    {
        return view('livewire.admin.progress.te.hard-step5');
    }
}
