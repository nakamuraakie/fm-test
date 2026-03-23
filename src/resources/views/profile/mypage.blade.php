@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-wrap">

  
  <div class="mypage-head">
    <div class="mypage-user">
      <div class="mypage-avatar">
        @if(!empty(optional($user->profile)->image_url))
          <img src="{{ Str::startsWith($user->profile->image_url, ['http://', 'https://']) ? $user->profile->image_url : asset('storage/' . $user->profile->image_url) }}" alt="icon">
        @else
          <span class="mypage-avatar-text">{{ mb_substr($user->name, 0, 1) }}</span>
        @endif
      </div>

      <div class="mypage-name">{{ $user->name }}</div>
    </div>

    <a href="{{ route('profile.edit') }}" class="mypage-edit-btn">プロフィールを編集</a>
  </div>


  @php
    use Illuminate\Support\Str;

    $sellUrl = url('/mypage?tab=sell');
    $buyUrl  = url('/mypage?tab=buy');
  @endphp

  <div class="tab-menu">
    <a href="{{ $sellUrl }}" class="tab-link {{ ($tab ?? request('tab','sell')) === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ $buyUrl }}" class="tab-link {{ ($tab ?? request('tab')) === 'buy' ? 'active' : '' }}">購入した商品</a>
  </div>


  <div class="items-list">
    @if(($tab ?? request('tab','sell')) === 'buy')
      @forelse($buyItems as $order)
        @php $item = $order->item; @endphp
        @if($item)
          <div class="item-card">
            <a href="{{ route('items.show', $item->id) }}" class="item-link">
              @if(!empty($item->image_url))
                <img src="{{ Str::startsWith($item->image_url, ['http://', 'https://']) ? $item->image_url : asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="item-img">
              @endif
              <p class="item-name">{{ $item->name }}</p>
              @if($item->status === 'sold')
                <span class="sold">Sold</span>
              @endif
            </a>
          </div>
        @endif
      @empty
        <p>購入した商品はまだありません。</p>
      @endforelse

      @else
      @forelse($sellItems as $item)
        <div class="item-card">
          <a href="{{ route('items.show', $item->id) }}" class="item-link">
            @if(!empty($item->image_url))
              <img src="{{ Str::startsWith($item->image_url, ['http://', 'https://']) ? $item->image_url : asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="item-img">
            @endif
            <p class="item-name">{{ $item->name }}</p>
            @if($item->status === 'sold')
              <span class="sold">Sold</span>
            @endif
          </a>
        </div>
      @empty
        <p>出品した商品はまだありません。</p>
      @endforelse
    @endif
  </div>

</div>
@endsection