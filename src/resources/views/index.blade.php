@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="items-wrap">

@php
  use Illuminate\Support\Str;

  $recommendUrl = route('index', array_merge(request()->query(), ['tab' => 'recommend']));
  $mylistUrl    = route('index', array_merge(request()->query(), ['tab' => 'mylist']));
@endphp

<div class="tab-menu">
  <a href="{{ $recommendUrl }}" class="tab-link {{ request('tab','recommend') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
  <a href="{{ $mylistUrl }}" class="tab-link {{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

<div class="items-list">
    @foreach ($items as $item)
        <div class="item-card">
            <a href="{{ route('items.show', $item->id) }}" class="item-link">
                @if (!empty($item->image_url))
                    <img
                        src="{{ Str::startsWith($item->image_url, ['http://', 'https://']) ? $item->image_url : asset('storage/' . $item->image_url) }}"
                        alt="{{ $item->name }}"
                        class="item-img">
                @endif

                <p class="item-name">{{ $item->name }}</p>

                @if ($item->status === 'sold')
                    <span class="sold">Sold</span>
                @endif
            </a>
        </div>
    @endforeach
</div>

</div>
@endsection