<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_name_is_required()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_email_is_required()
    {
        // 1. 会員登録ページから送信したと仮定し、メールアドレスを空にしてPOSTリクエストを送る
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => '', // 2. メールアドレスを未入力にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 3. 「メールアドレスを入力してください」というメッセージがセッションにあるか確認
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    public function test_password_is_required()
    {
        // 1. 会員登録ページからリクエストを送る準備
        // 2. パスワードを空にして、他の項目（名前・メール）は正しく入力する
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '', // 空にする
            'password_confirmation' => '', // 確認用も空にする
        ]);

        // 3. 「パスワードを入力してください」というエラーが返ってきているか確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    public function test_password_length_min_8()
    {
        // 1. 会員登録ページからリクエストを送る準備
        // 2. 7文字以下のパスワード（例: 'short12'）を入力する
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'short12', // 7文字
            'password_confirmation' => 'short12',
        ]);

        // 3. 「パスワードは8文字以上で入力してください」というエラーが返ってきているか確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);
    }

    public function test_password_matching()
    {
        // 1. 会員登録ページからリクエストを送る準備
        // 2. 確認用パスワードと異なるパスワードを入力する
        $response = $this->from('/register')->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_pass', // 一致しない値を入力
        ]);

        // 3. 「パスワードと一致しません」というエラーが返ってきているか確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません'
        ]);
    }

    public function test_user_can_register_and_redirect_to_profile_setting()
    {
        // 1. テスト用のデータを用意する
        $userData = [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 2. 会員登録処理（POST）を実行
        $response = $this->post('/register', $userData);

        // 3. データベースにユーザーが登録されたか確認
        $this->assertDatabaseHas('users', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
        ]);

        // 4. 指定のプロフィール設定画面（例: /profile）へリダイレクトされるか確認
        // ※プロジェクトの実際の遷移先パスに合わせて変更してください
        $response->assertRedirect('/mypage/profile');
    }
}
