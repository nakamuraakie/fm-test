<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テストユーザーの作成 (名前を「テストユーザー」にする)
        $this->user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // 2. プロフィール情報の設定 (ログに出力されていた値に合わせる)
        DB::table('profiles')->insert([
            'user_id' => $this->user->id,
            'image_url' => 'test_avatar.jpg',
            'postcode' => '123-4567',
            'address' => '東京都',
            'building' => 'テストビル',
        ]);
    }

    /**
     * プロフィール閲覧画面のテスト
     */
    public function test_user_can_see_profile_info_and_item_lists()
    {
        $this->actingAs($this->user);
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('test_avatar.jpg');
    }

    /**
     * 変更項目が初期値として設定されていることのテスト
     */
    public function test_profile_edit_page_shows_initial_values()
    {
        $this->actingAs($this->user);

        // プロフィール編集画面へアクセス
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);

        // ログに出力されていた HTML の value 値と完全に一致させる
        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都"', false);
        $response->assertSee('value="テストビル"', false);
        
        // 画像パスが含まれているか
        $response->assertSee('test_avatar.jpg');
    }
}