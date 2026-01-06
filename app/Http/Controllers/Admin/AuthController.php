<?php
namespace App\Http\Controllers\Admin;

use App\Services\SsoTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function __construct(
        protected SsoTokenService $ssoTokenService
    ) {}

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

            // SSOトークンを生成してCake側にリダイレクト
            $user = Auth::guard('admin')->user();
            $rawToken = $this->ssoTokenService->createToken($user->id);
            $baseUrl = $request->getSchemeAndHttpHost();
            $ssoUrl = $this->ssoTokenService->buildSsoLoginUrl($baseUrl, $rawToken);

            return redirect()->away($ssoUrl);
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

        // Cake側もログアウトさせる（その後Laravelのログイン画面に戻る）
        $baseUrl = $request->getSchemeAndHttpHost();
        $laravelLoginUrl = route('admin.login');
        $ssoLogoutUrl = $baseUrl . '/app/admin/users/sso-logout?redirect=' . urlencode($laravelLoginUrl);

        return redirect()->away($ssoLogoutUrl);
    }
}
