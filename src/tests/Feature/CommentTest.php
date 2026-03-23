<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // ユーザー作成
        $this->user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);
        
        // 商品作成に必要な状態データを準備
        DB::table('conditions')->insert(['id' => 1, 'condition' => '良好']);
        
        // テスト用商品の作成
        $this->item = Item::create([
            'user_id' => $this->user->id,
            'name' => 'コメントテスト商品',
            'brand' => 'ブランドA',
            'price' => 1000,
            'description' => 'テスト説明文',
            'condition_id' => 1,
            'image_url' => 'test.jpg',
        ]);
    }

    /**
     * 1. ログイン済みのユーザーはコメントを送信できる
     */
    public function test_logged_in_user_can_send_comment()
    {
        $this->actingAs($this->user);

        // コメント送信（POSTリクエスト）
        $response = $this->post("/item/{$this->item->id}/comment", [
            'comment' => 'これはテストコメントです'
        ]);

        // DBに保存されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'comment' => 'これはテストコメントです'
        ]);

        // 詳細ページでコメント数が増加しているか確認
        $response = $this->get("/item/{$this->item->id}");
        $response->assertSee('1'); // コメント件数
    }

    /**
     * 2. ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_send_comment()
    {
        // ログインせずに送信
        $response = $this->post("/item/{$this->item->id}/comment", [
            'comment' => '未ログインのコメント'
        ]);

        // ログインページへリダイレクトされるか、あるいは保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'comment' => '未ログインのコメント'
        ]);
    }

    /**
     * 3. コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_is_required()
    {
        $this->actingAs($this->user);

        // 空で送信
        $response = $this->from("/item/{$this->item->id}")
                    ->post("/item/{$this->item->id}/comment", [
            'comment' => ''
        ]);

        // バリデーションエラーがあるか確認
        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * 4. コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_max_length()
    {
        $this->actingAs($this->user);

        // 256文字のコメントを作成
        $longComment = str_repeat('あ', 256);

        $response = $this->from("/item/{$this->item->id}")
                         ->post("/item/{$this->item->id}/comment", [
            'comment' => $longComment
        ]);

        // バリデーションエラーがあるか確認
        $response->assertSessionHasErrors(['comment']);
    }
}