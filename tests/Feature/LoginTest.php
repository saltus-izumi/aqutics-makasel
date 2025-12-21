<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_with_existing_user()
    {
        // 既存ユーザー取得
        $user = User::where('user_account', 'izumi@saltus.jp')->first();
        $this->assertNotNull($user, '対象ユーザーが見つかりません');

        $response = $this->withMiddleware()
            ->post('/login', [
                'email' => 'izumi@saltus.jp',
                'password'     => 'izumitest',
            ]);

            // ここが最重要：ログイン失敗ならバリデーションエラーが入る
        $response->assertSessionHasNoErrors();

        // 302になってるか（成功時は基本リダイレクト）
        $response->assertStatus(302);

        // 認証されているか
        $this->assertTrue(Auth::check(), 'Auth::check() が false のままです');
        $this->assertAuthenticatedAs($user);
    }
}
