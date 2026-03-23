@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_address.css') }}">
@endsection

@section('content')
<div class="address-page">
  <h2 class="address-title">住所の変更</h2>

  <form method="POST" action="/purchase/address/{{ $item->id }}" class="address-form">
    @csrf

    <div class="form-group">
      <label for="postcode" class="form-label">郵便番号</label>
      <input type="text" name="postcode" id="postcode" class="form-input">
      @error('postcode')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="address" class="form-label">住所</label>
      <input type="text" name="address" id="address" class="form-input">
      @error('address')
        <p class="error">{{ $message }}</p>
      @enderror
    </div>

    <div class="form-group">
      <label for="building" class="form-label">建物名</label>
      <input type="text" name="building" id="building" class="form-input">
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-main">更新する</button>
    </div>
  </form>
</div>
@endsection