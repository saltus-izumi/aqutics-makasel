<?php

namespace App\Livewire\Admin\Master\MailTemplate;

use App\Models\MailTemplate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditTemplate extends Component
{
    public string $mailKind = '';
    public string $subject = '';
    public string $body = '';

    public function mount(): void
    {
    }

    protected function rules(): array
    {
        return [
            'mailKind' => ['required', Rule::in(array_keys(MailTemplate::MAIL_KIND))],
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'mailKind' => 'メール種別',
            'subject' => '件名',
            'body' => '本文',
        ];
    }

    public function updatedMailKind($value): void
    {
        if ($value === '' || $value === null) {
            $this->subject = '';
            $this->body = '';
            return;
        }

        $mailTemplate = MailTemplate::query()
            ->where('mail_kind', (int) $value)
            ->first();

        $this->subject = $mailTemplate?->subject ?? '';
        $this->body = $mailTemplate?->body ?? '';
    }

    public function save(): void
    {
        $validated = $this->validate();

        $mailTemplate = MailTemplate::query()
            ->firstOrNew(['mail_kind' => (int) $validated['mailKind']]);

        $mailTemplate->mail_kind = (int) $validated['mailKind'];
        $mailTemplate->subject = $validated['subject'];
        $mailTemplate->body = $validated['body'];
        $mailTemplate->save();
    }

    public function render()
    {
        return view('livewire.admin.master.mail-template.edit-template');
    }
}
