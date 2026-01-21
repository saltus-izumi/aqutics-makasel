<?php
namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function create()
    {
        return view('owner.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'mail' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::guard('owner')->attempt(
            ['mail' => $credentials['mail'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            // セッション固定化対策（公式推奨）
            $request->session()->regenerate();

            return redirect()->intended(route('owner.dashboard'));
        }

        return back()->withErrors([
            'mail' => __('auth.failed'),
        ])->onlyInput('mail');
    }

    public function destroy(Request $request)
    {
        Auth::guard('owner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }
}
