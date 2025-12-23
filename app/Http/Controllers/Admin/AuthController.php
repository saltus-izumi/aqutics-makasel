<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function index()
    {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'user_account' => ['required'],
            'user_password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt(
            ['user_account' => $credentials['user_account'], 'password' => $credentials['user_password']],
            $request->boolean('remember')
        )) {
            // セッション固定化対策（公式推奨）
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'user_account' => __('auth.failed'),
        ])->onlyInput('user_account');
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
