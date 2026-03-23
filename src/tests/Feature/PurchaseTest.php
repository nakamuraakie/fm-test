<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;
    protected $orderBaseData;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. ユーザーとプロフィールの作成（住所が '---' だとコントローラーで弾かれるため設定）
        $this->user = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password')
        ]);
        
        DB::table('profiles')->insert([
            'user_id' => $this->user->id,
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);

        $seller = User::create(['name' => '出品者', 'email' => 'seller@example.com', 'password' => bcrypt('password')]);
        DB::table('conditions')->insert(['id' => 1, 'condition' => '良好']);
        
        $this->item = Item::create([
            'user_id' => $seller->id,
            'name' => '購入テスト商品',
            'brand' => 'ブランドX',
            'price' => 3000,
            'description' => 'テスト用',
            'condition_id' => 1,
            'image_url' => 'test_item.jpg',
            'status' => 'available'
        ]);

        $this->orderBaseData = [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都渋谷区',
            'sending_building' => 'テストビル',
            'payment_method' => 'card',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * 1. 購入完了テスト
     */
    public function test_user_can_purchase_item()
    {
        $this->actingAs($this->user);

        // Stripeへの外部リダイレクトをテストで通すため、例外処理を無効化せず実行
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
        ]);

        // ステータスコード 302 (Stripeへのリダイレクト) を確認
        $response->assertStatus(302);

        // DBに注文が入っているか確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 商品ステータスが sold になっているか確認
        $this->assertEquals('sold', $this->item->fresh()->status);
    }

    /**
     * 2. 商品一覧画面にて「Sold」と表示される
     */
    public function test_purchased_item_is_labeled_as_sold_on_index()
    {
        // 注文データを作成し、商品のステータスも更新する
        DB::table('orders')->insert($this->orderBaseData);
        $this->item->update(['status' => 'sold']);

        $response = $this->get("/");
        $response->assertStatus(200);
        
        // Bladeの @if ($item->status === 'sold') に合致するように確認
        $response->assertSee('Sold');
    }

    /**
     * 3. 購入した商品一覧に追加されている
     */
    public function test_purchased_item_appears_in_profile_list()
    {
        DB::table('orders')->insert($this->orderBaseData);

        $this->actingAs($this->user);
        $response = $this->get("/mypage?tab=buy");

        $response->assertStatus(200);
        $response->assertSee('購入テスト商品');
    }

    public function test_selected_payment_method_is_reflected()
    {
        $this->actingAs($this->user);

        // 1. 支払い方法を「コンビニ払い」に設定（セッションやリクエストを想定）
        // アプリの仕様に合わせて、ここではクエリパラメータやセッションを利用します
        $paymentMethod = 'convenience';
        
        // 2. 購入画面を開く（支払い方法を指定してアクセス）
        $response = $this->get("/purchase/{$this->item->id}?payment_method={$paymentMethod}");

        $response->assertStatus(200);

        // 3. 選択した支払い方法が画面上の「支払い方法」セクションに表示されているか確認
        // Blade側で {{ $payment_method }} のように表示している箇所をチェックします
        $response->assertSee('コンビニ払い');
    }


    public function test_changed_address_is_reflected_on_purchase_page()
    {
        $this->actingAs($this->user);

        // 新しい住所をセッションに保存（住所変更画面での「更新」動作をシミュレート）
        $newAddress = [
            'postcode' => '999-8888',
            'address'  => '新住所',
            'building' => '新ビル',
        ];
        session(["purchase_address.{$this->item->id}" => $newAddress]);

        // 購入画面を開く
        $response = $this->get("/purchase/{$this->item->id}");

        $response->assertStatus(200);
        
        // 画面上に新しい住所が表示されているか確認
        $response->assertSee('999-8888');
        $response->assertSee('新住所');
        $response->assertSee('新ビル');
    }

    /**
     * 2. 登録した住所で購入した際、正しく注文データに紐付いている
     */
    public function test_order_is_created_with_changed_address()
    {
        $this->actingAs($this->user);

        // 新しい住所をセッションに設定
        $newAddress = [
            'postcode' => '777-6666',
            'address'  => '配送先住所',
            'building' => '配送先ビル',
        ];
        session(["purchase_address.{$this->item->id}" => $newAddress]);

        // 購入を実行
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
        ]);

        $response->assertStatus(302); // Stripeへリダイレクト

        // データベースの「orders」テーブルに、セッションの住所が保存されているか確認
        $this->assertDatabaseHas('orders', [
            'user_id'          => $this->user->id,
            'item_id'          => $this->item->id,
            'sending_postcode' => '777-6666',
            'sending_address'  => '配送先住所',
            'sending_building' => '配送先ビル',
        ]);
    }

}

