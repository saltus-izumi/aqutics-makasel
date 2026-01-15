<?php

namespace App\View\Components\User;

use Closure;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class ProfileIcon extends Component
{
    public ?User $user;
    public ?string $imageDataUrl;
    public ?string $initial;
    public string $bgColor;

    /**
     * Create a new component instance.
     */
    public function __construct(?int $id = null)
    {
        $this->user = $id ? User::find($id) : null;
        $this->imageDataUrl = null;

        if ($this->user?->profile_image_file_path) {
            $path = $this->user->profile_image_file_path;
dump($path);
dump(Storage::disk('local')->path($path));

            if (Storage::disk('local')->exists($path)) {
                $fullPath = Storage::disk('local')->path($path);
dump($fullPath);
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $contents = Storage::disk('local')->get($path);
                $this->imageDataUrl = 'data:' . $mime . ';base64,' . base64_encode($contents);
            }
        }

        $name = $this->user?->user_name ?? '';
        $this->initial = $name !== '' ? mb_substr($name, 0, 1) : null;
        $this->bgColor = $this->user?->profile_bgcolor ?? '55aaaa';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user.profile-icon');
    }
}
