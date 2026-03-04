<?php

namespace App\Livewire\Admin\Progress\Ge;

use App\Models\GeProgress;
use App\Models\GeProgressFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Memo extends Component
{
    use WithFileUploads;

    public $geProgress = null;
    public $memo;

    public string $componentId = '';

    protected $listeners = ['geProgressUpdated' => 'reloadProgress'];
    protected array $geProgressMap = [
        'memo' => 'memo',
    ];
    protected function rules(): array
    {
        return [
            'memo' => ['nullable', 'string'],
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
        $this->memo = $geProgress?->memo;
        $this->componentId = $this->getId();
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
        return view('livewire.admin.progress.ge.memo');
    }
}
