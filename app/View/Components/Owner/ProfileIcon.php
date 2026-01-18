<?php

namespace App\View\Components\Owner;

use Closure;
use App\Models\Owner;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class ProfileIcon extends Component
{
    public ?Owner $owner;
    public ?string $imageDataUrl;
    public ?string $initial;
    public string $bgColor;

    /**
     * Create a new component instance.
     */
    public function __construct(?int $id = null)
    {
        $this->owner = $id ? Owner::find($id) : null;
        $this->imageDataUrl = null;

        if ($this->owner?->profile_image_file_path) {
            $path = $this->owner->profile_image_file_path;

            if (Storage::disk('local')->exists($path)) {
                $fullPath = Storage::disk('local')->path($path);
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $contents = Storage::disk('local')->get($path);
                $this->imageDataUrl = 'data:' . $mime . ';base64,' . base64_encode($contents);
            }
        }

        $name = $this->owner?->name ?? '';
        $this->initial = $name !== '' ? mb_substr($name, 0, 1) : null;
        $this->bgColor = $this->owner?->profile_bg_color ?? '55aaaa';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.owner.profile-icon');
    }
}
