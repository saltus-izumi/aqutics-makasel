<?php

namespace App\Livewire\Admin\Master\MailTemplate;

use Livewire\Component;

class EditTemplate extends Component
{
    public $mailKinds = null;

    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.admin.master.mail-template.edit-template');
    }
}
