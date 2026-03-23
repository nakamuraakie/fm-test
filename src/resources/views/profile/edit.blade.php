@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">


<div class="profile-edit">

  <h1>プロフィール設定</h1>

  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')


<div class="field profile-row">


  <div class="preview">
  @if(!empty($profile->image_url))
    <img src="{{ Storage::url($profile->image_url) }}" class="profile-img">
  @else
    <div class="profile-img default-avatar"></div>
  @endif
</div>


  <div class="image-side">
    <input id="imageInput" type="file" name="image" accept="image/*" hidden>
    <label for="imageInput" class="image-btn">
      画像を選択する
    </label>

    @error('image')
      <p class="error">{{ $message }}</p>
    @enderror
  </div>

</div>


    <div class="field">
      <label>ユーザー名</label>
      <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}">
      @error('name')<p class="error">{{ $message }}</p>@enderror
    </div>


    <div class="field">
      <label>郵便番号</label>
      <input type="text" name="postcode" value="{{ old('postcode', $profile->postcode ?? '') }}">
      @error('postcode')<p class="error">{{ $message }}</p>@enderror
    </div>


    <div class="field">
      <label>住所</label>
      <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
      @error('address')<p class="error">{{ $message }}</p>@enderror
    </div>


    <div class="field">
      <label>建物名</label>
      <input type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
      @error('building')<p class="error">{{ $message }}</p>@enderror
    </div>

    <button type="submit">更新する</button>
  </form>

</div>
@endsection