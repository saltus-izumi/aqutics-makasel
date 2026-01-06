<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SsoTokenService
{
    /**
     * CakeのSECURITY_SALT（.envから取得）
     */
    protected string $cakeSalt;

    /**
     * トークンの有効期限（秒）
     */
    protected int $expiresInSeconds = 60;

    public function __construct()
    {
        $this->cakeSalt = config('services.cake.security_salt', '');
    }

    /**
     * SSOトークンを生成してDBに保存
     *
     * @param int $userId ログインさせるユーザーID
     * @return string 生の（ハッシュ化前の）トークン
     */
    public function createToken(int $userId): string
    {
        // ランダムなトークンを生成
        $rawToken = Str::random(64);

        // Cake側と同じロジックでハッシュ化
        $tokenDigest = hash('sha256', $rawToken . $this->cakeSalt);

        $now = Carbon::now();

        // DBに保存
        DB::table('sso_tokens')->insert([
            'user_id' => $userId,
            'token_digest' => $tokenDigest,
            'expires_at' => $now->copy()->addSeconds($this->expiresInSeconds),
            'consumed' => false,
            'consumed_at' => null,
            'created' => $now,
            'modified' => $now,
        ]);

        return $rawToken;
    }

    /**
     * CakeのSSOログインURLを生成
     *
     * @param string $baseUrl ベースURL（例: http://local.zen.inc）
     * @param string $rawToken 生のトークン
     * @param string|null $redirect ログイン後のリダイレクト先
     * @return string
     */
    public function buildSsoLoginUrl(string $baseUrl, string $rawToken, ?string $redirect = null): string
    {
        $url = $baseUrl . '/app/admin/users/sso-login?token=' . urlencode($rawToken);

        if ($redirect) {
            $url .= '&redirect=' . urlencode($redirect);
        }

        return $url;
    }
}
