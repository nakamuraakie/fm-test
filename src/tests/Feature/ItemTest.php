<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 各テストが実行される前に毎回自動で呼ばれる
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 商品の状態（condition_id = 1）をあらかじめ作成しておく
        DB::table('conditions')->insert([
            'id' => 1,
            'condition' => '良好',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // もしカテゴリ(category_id)も必須ならここに追加
        // DB::table('categories')->insert(['id' => 1, 'name' => 'テストカテゴリ']);
    }

    /**
     * 要件1：全商品を取得できる
     */
    public function test_can_get_all_items()
    {
        $user = User::create(['name' => 'Seller', 'email' => 'seller_all@example.com', 'password' => bcrypt('password')]);

        Item::create([
            'user_id' => $user->id,
            'name' => '見つかるべき商品',
            'price' => 2000,
            'description' => 'テスト',
            'condition_id' => 1, // setUpで作ったID
            'image_url' => 'test.jpg',
            'status' => 'active',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('見つかるべき商品');
    }

    /**
     * 要件2：購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_on_purchased_items()
    {
        $user = User::create(['name' => 'Seller', 'email' => 'seller_s@example.com', 'password' => bcrypt('password')]);

        Item::create([
            'user_id' => $user->id,
            'name' => '売却済み商品',
            'price' => 1000,
            'description' => 'テスト',
            'condition_id' => 1,
            'image_url' => 'test.jpg',
            'status' => 'sold',
        ]);

        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    /**
     * 要件3：自分が出品した商品は表示されない
     */
    public function test_own_items_are_not_displayed_in_list()
    {
        $me = User::create(['name' => '自分', 'email' => 'me@example.com', 'password' => bcrypt('password')]);
        $other = User::create(['name' => '他人', 'email' => 'other@example.com', 'password' => bcrypt('password')]);

        $this->actingAs($me);

        Item::create([
            'user_id' => $me->id,
            'name' => '自分の出品物',
            'price' => 500,
            'description' => 'テスト',
            'condition_id' => 1,
            'image_url' => 'test.jpg',
        ]);
        
        Item::create([
            'user_id' => $other->id,
            'name' => '他人の出品物',
            'price' => 800,
            'description' => 'テスト',
            'condition_id' => 1,
            'image_url' => 'test.jpg',
        ]);

        $response = $this->get('/');
        $response->assertSee('他人の出品物');
        $response->assertDontSee('自分の出品物');
    }



    public function test_mylist_shows_only_favorited_items()
    {
        $me = User::create(['name' => '自分', 'email' => 'me_fav@example.com', 'password' => bcrypt('password')]);
        $other = User::create(['name' => '他人', 'email' => 'other_fav@example.com', 'password' => bcrypt('password')]);

        $favItem = Item::create(['user_id' => $other->id, 'name' => 'いいねした商品', 'price' => 1000, 'description' => 'いいね説明', 'condition_id' => 1, 'category_id' => 1, 'image_url' => 'test.jpg']);
        $normalItem = Item::create(['user_id' => $other->id, 'name' => '通常の商品', 'price' => 2000, 'description' => '通常説明', 'condition_id' => 1, 'category_id' => 1, 'image_url' => 'test.jpg']);

        // 中間テーブル favorites にデータを挿入
        DB::table('favorites')->insert([
            'user_id' => $me->id,
            'item_id' => $favItem->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($me);
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('通常の商品');
    }

    /**
     * 要件：マイリストでも購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_on_mylist()
    {
        $me = User::create(['name' => '自分', 'email' => 'me_msold@example.com', 'password' => bcrypt('password')]);
        $seller = User::create(['name' => '売主', 'email' => 'seller_msold@example.com', 'password' => bcrypt('password')]);

        $soldItem = Item::create([
            'user_id' => $seller->id,
            'name' => '売却済みのいいね商品',
            'price' => 1500,
            'description' => '売却済み説明',
            'condition_id' => 1,
            'category_id' => 1,
            'image_url' => 'test.jpg',
            'status' => 'sold'
        ]);

        DB::table('favorites')->insert(['user_id' => $me->id, 'item_id' => $soldItem->id]);

        $this->actingAs($me);
        $response = $this->get('/?tab=mylist');

        $response->assertSee('売却済みのいいね商品');
        $response->assertSee('Sold');
    }

    /**
     * 要件：未認証の場合は何も表示されない
     */
    public function test_mylist_shows_nothing_when_not_logged_in()
    {
        // ログインせずにマイリストタブへ
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        // 商品カード（item-card）が表示されていないことを検証
        $response->assertDontSee('item-card');
    }



    public function test_can_search_items_by_name()
    {
        $user = User::create(['name' => 'Seller', 'email' => 'search@example.com', 'password' => bcrypt('password')]);

        // 検索にヒットする商品
        Item::create([
            'user_id' => $user->id, 'name' => '限定のスニーカー', 'price' => 1000, 
            'description' => '検索用', 'condition_id' => 1, 'category_id' => 1, 'image_url' => 'test.jpg'
        ]);

        // 検索にヒットしない商品
        Item::create([
            'user_id' => $user->id, 'name' => 'ただの帽子', 'price' => 500, 
            'description' => '検索対象外', 'condition_id' => 1, 'category_id' => 1, 'image_url' => 'test.jpg'
        ]);

        // 「スニーカー」というキーワードで検索（クエリパラメータ ?keyword=...）
        $response = $this->get('/?keyword=スニーカー');

        $response->assertStatus(200);
        $response->assertSee('限定のスニーカー');
        $response->assertDontSee('ただの帽子');
    }

    /**
     * 要件：検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_is_retained_when_switching_to_mylist()
    {
        $me = User::create(['name' => '自分', 'email' => 'me_search@example.com', 'password' => bcrypt('password')]);
        $this->actingAs($me);

        // 1. まずキーワードを入れて検索（おすすめタブ）
        $response = $this->get('/?keyword=スニーカー');
        $response->assertStatus(200);

        // 2. そのままマイリストタブへ遷移（URLにkeywordが含まれていることを期待）
        // 実際の実装では、リンクやフォームのhidden等でkeywordを引き継ぐ必要があります
        $responseMylist = $this->get('/?tab=mylist&keyword=スニーカー');

        $responseMylist->assertStatus(200);
        // 画面上の検索窓（inputタグなど）にキーワードが残っているか確認
        $responseMylist->assertSee('value="スニーカー"', false); 
    }

}