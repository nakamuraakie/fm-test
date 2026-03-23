<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. 商品の状態を準備
        DB::table('conditions')->insert([
            'id' => 1,
            'condition' => '良好',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. カテゴリを複数準備
        DB::table('categories')->insert([
            ['id' => 1, 'category' => 'テストカテゴリ', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'category' => 'ファッション', 'created_at' => now(), 'updated_at' => now()],
        ]);


    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_item_detail_page_displays_all_required_information()
    {
        $seller = User::create(['name' => '出品者', 'email' => 'seller@example.com', 'password' => bcrypt('password')]);
        $commenter = User::create(['name' => 'コメント太郎', 'email' => 'commenter@example.com', 'password' => bcrypt('password')]);

        // 商品の作成
        $item = Item::create([
            'user_id' => $seller->id,
            'name' => '詳細テスト商品',
            'brand' => 'ナイキ',
            'price' => 5000,
            'description' => 'これは詳細ページのテスト用説明文です。',
            'condition_id' => 1,
            'image_url' => 'detail_test.jpg',
        ]);

        // 複数カテゴリの紐付け
        $item->categories()->attach([1, 2]);

        // いいねとコメント
        DB::table('favorites')->insert(['user_id' => $commenter->id, 'item_id' => $item->id]);
        DB::table('comments')->insert([
            'user_id' => $commenter->id,
            'item_id' => $item->id,
            'comment' => 'この商品最高ですね！',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 実行
        $response = $this->get("/item/{$item->id}");

        // 検証
        $response->assertStatus(200);
        $response->assertSee('詳細テスト商品');
        $response->assertSee('ナイキ');
        $response->assertSee('5,000');
        $response->assertSee('これは詳細ページのテスト用説明文です。');
        $response->assertSee('良好');
        $response->assertSee('テストカテゴリ');
        $response->assertSee('ファッション');
        $response->assertSee('コメント太郎');
        $response->assertSee('この商品最高ですね！');
        $response->assertSee('1'); // いいね数とコメント数
    }


    public function test_user_can_list_item_with_all_required_info()
    {
        $this->actingAs($this->user);

        // ストレージのフェイク（実際にファイルを保存させない）
        Storage::fake('public');
        $file = UploadedFile::fake()->image('item_image.jpg');

        // 出品リクエストのデータ
        $postData = [
            'name'         => 'テスト商品名',
            'brand'        => 'テストブランド',
            'description'  => '商品の説明文です。',
            'price'        => 5000,
            'condition_id' => 1,
            'category_ids' => [1, 2], // カテゴリは複数選択を想定
            'image'        => $file,
        ];

        // 商品保存を実行（POSTリクエスト）
        $response = $this->post('/sell', $postData);

        // 保存後のリダイレクト先を確認（例: トップページや詳細ページ）
        $response->assertStatus(302);

        // 1. itemsテーブルに基本情報が保存されているか
        $this->assertDatabaseHas('items', [
            'user_id'     => $this->user->id,
            'name'        => 'テスト商品名',
            'brand'       => 'テストブランド',
            'price'       => 5000,
            'description' => '商品の説明文です。',
            'condition_id' => 1,
        ]);

        // 2. 中間テーブル（category_itemなど）にカテゴリが紐付いているか
        $item = Item::where('name', 'テスト商品名')->first();
        $this->assertCount(2, $item->categories);
        $this->assertTrue($item->categories->contains(1));
        $this->assertTrue($item->categories->contains(2));

        // 3. 画像がストレージに保存されているか
        // コントローラーでの保存パスに合わせて調整してください
        $this->assertNotNull($item->image_url);
    }

}
