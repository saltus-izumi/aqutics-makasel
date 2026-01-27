<?php

namespace App\Livewire\Admin\Import;

use Livewire\Component;
use Livewire\WithFileUploads;

class ProcallAdd extends Component
{
    use WithFileUploads;

    public $procallFile;

    protected $messages = [
        'procallFile.required' => 'ファイルを選択してください。',
    ];

    public function import(): void
    {
        $this->validate([
            'procallFile' => ['required', 'file'],
        ]);

        // TODO: 実際の取込処理に置き換える
        $path = $this->procallFile->store('imports');
        $this->reset('procallFile');
    }

    public function render()
    {
        return view('livewire.admin.import.procall-add');
    }
}
