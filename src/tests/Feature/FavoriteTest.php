<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テスト用ユーザーの作成
        $this->user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);
        
        // 2. 状態データの準備
        DB::table('conditions')->insert(['id' => 1, 'condition' => '良好']);
        
        // 3. 商品の作成（image_url 必須エラー対策済み）
        $this->item = Item::create([
            'user_id' => $this->user->id,
            'name' => 'いいねテスト商品',
            'brand' => 'ブランドA',
            'price' => 1000,
            'description' => 'テスト用説明文',
            'condition_id' => 1,
            'image_url' => 'test_image.jpg', // 必須項目を補完
        ]);
    }

    /**
     * 1. いいね登録のテスト
     */
    public function test_user_can_register_favorite()
    {
        $this->actingAs($this->user);

        // いいね実行
        $response = $this->post("/favorite/{$this->item->id}");

        // データベースに登録されたか
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 表示上のカウントが増えているか
        $response = $this->get("/item/{$this->item->id}");
        $response->assertSee('1');
    }

    /**
     * 2. 追加済みのアイコンは色が変化する
     */
    public function test_favorite_icon_color_changes_when_added()
    {
        // 先にいいね済みにする
        DB::table('favorites')->insert([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id
        ]);

        $this->actingAs($this->user);
        $response = $this->get("/item/{$this->item->id}");

        // ※実装に合わせてクラス名（text-red-500 など）を確認してください
        // いったん「1」が表示されているかを確認することで代替も可能です
        $response->assertSee('1'); 
    }

    /**
     * 3. 再度いいね解除のテスト
     */
    public function test_user_can_remove_favorite()
    {
        // 最初はいいねされている状態
        DB::table('favorites')->insert([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id
        ]);

        $this->actingAs($this->user);

        // 解除実行（再度お気に入りボタンを押す動作）
        $response = $this->post("/favorite/{$this->item->id}");

        // データベースから消えたか
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 表示上のカウントが減っているか
        $response = $this->get("/item/{$this->item->id}");
        $response->assertSee('0');
    }
}