<?php

namespace App\Livewire\Admin\Progress\En;

use App\Models\EnProgress;
use App\Models\EnProgressFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Memo extends Component
{
    use WithFileUploads;

    public $enProgress = null;
    public $memo;

    public string $componentId = '';

    protected $listeners = ['enProgressUpdated' => 'reloadProgress'];
    protected array $enProgressMap = [
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

    public function mount($enProgress)
    {
        $this->enProgress = $enProgress;
        $this->memo = $enProgress?->memo;
        $this->componentId = $this->getId();
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

        $value = trim($value) ? trim($value) : null;

        $column = $this->enProgressMap[$propertyName];
        $this->enProgress->{$column} = $value;
        $this->enProgress->save();

        $this->dispatch('enProgressUpdated', enProgressId: $this->enProgress->id);
    }

    public function reloadProgress($enProgressId = null)
    {
        if (!$this->enProgress) {
            return;
        }

        if ($enProgressId !== null && (int) $enProgressId !== (int) $this->enProgress->id) {
            return;
        }

        $this->enProgress = EnProgress::query()
            ->find($this->enProgress->id);
    }

    public function render()
    {
        return view('livewire.admin.progress.en.memo');
    }
}
