@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="item-show">


  <div class="item-top">
    @php use Illuminate\Support\Str; @endphp

<div class="item-left">
    <div class="item-image-wrap">
        @if (!empty($item->image_url))
        <img src="{{ Str::startsWith($item->image_url, ['http://', 'https://']) ? $item->image_url : asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="item-image">
        @endif
        @if ($item->status === 'sold')
            <span class="badge-sold">Sold</span>
        @endif
    </div>
</div>

    <div class="item-right">
      <h1 class="item-title">{{ $item->name }}</h1>
      @if($item->brand)
        <p class="item-brand">{{ $item->brand }}</p>
      @endif
      <p class="item-price">
        ¥{{ number_format($item->price) }}
        <span class="tax-label">（税込）</span>
      </p>

      <div class="icon-row">
        <form action="/favorite/{{ $item->id }}" method="POST" class="icon-form">
          @csrf
          <button class="icon-btn like-btn {{ $isLiked ? 'liked' : '' }}" type="submit">
            <img src="{{ asset('images/ハートロゴ_' . ($isLiked ? 'ピンク' : 'デフォルト') . '.png') }}" alt="いいね" class="icon-img">
            <span class="count">{{ $likeCount }}</span>
          </button>
        </form>
        <a class="icon-btn" href="#comments">
          <img src="{{ asset('images/ふきだしロゴ.png') }}" alt="コメント" class="icon-img">
          <span class="count">{{ $commentCount ?? 0 }}</span>
        </a>
      </div>

      @if ($item->status === 'sold')
      <button class="purchase-btn is-disabled" type="button" disabled>売り切れ</button>
      @else
        <a href="{{ route('purchase.show', $item->id) }}" class="purchase-btn">購入手続きへ</a>
      @endif

      <section class="block">
        <h2 class="block-title">商品説明</h2>
        <p class="block-text">{{ $item->description }}</p>
      </section>


      <section class="block">
        <h2 class="block-title">商品情報</h2>
        <div class="info-grid">
          <div class="info-row">
            <span class="info-label">カテゴリー</span>
            <div class="info-value">
              @if($item->categories->count())
              @foreach($item->categories as $category)
              <span class="pill">{{ $category->category }}</span>
              @endforeach
              @else
              <span class="pill">未設定</span>
              @endif
            </div>
          </div>

          <div class="info-row">
            <span class="info-label">商品の状態</span>
            <div class="info-value">
            {{ optional($item->condition)->condition ?? '未設定' }}
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>


  <section id="comments" class="comments">
    <h2 class="comments-title">
  コメント
  <span class="comment-count">({{ $commentCount ?? 0 }})</span>
</h2>

    @if(isset($comments) && $comments->count())
      @foreach($comments as $comment)
        <div class="comment-card">
          <div class="comment-user">
            <div class="avatar">{{ mb_substr($comment->user->name, 0, 1) }}</div>
            <div>
              <p class="user-name">{{ $comment->user->name }}</p>
              <p class="comment-date">{{ $comment->created_at->format('Y/m/d H:i') }}</p>
            </div>
          </div>

          <p class="comment-body">{{ $comment->comment }}</p>


          @if(!empty($comment->reply))
            <div class="reply">
              <p class="reply-label">出品者からの返事</p>
              <p class="reply-body">{{ $comment->reply }}</p>
            </div>
          @endif
        </div>
      @endforeach
    @else
      <p class="empty">まだコメントはありません。</p>
    @endif
  </section>


  <section class="comment-form">
    <h2 class="block-title">商品へのコメント</h2>
    <form method="POST" action="{{ route('comment.store', $item->id) }}">
      @csrf

      <textarea name="comment" rows="4" class="textarea" placeholder="コメントを入力">{{ old('comment') }}</textarea>
      @error('comment')
        <p class="comment-error">{{ $message }}</p>
      @enderror

      <button type="submit" class="btn-secondary">コメントを送信する</button>
    </form>
  </section>

</div>
@endsection


