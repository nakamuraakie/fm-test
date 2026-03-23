@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase">

  <form action="{{ route('purchase.store', $item) }}" method="POST" class="purchase-grid">
    @csrf


    <section class="purchase-left">


      <div class="purchase-section">
        <div class="purchase-item-top">
          <div class="purchase-item-image">
            <img src="{{ asset($item->image_url) }}" alt="{{ $item->name }}">
          </div>

          <div class="purchase-item-info">
            <h1 class="purchase-item-name">{{ $item->name }}</h1>
            <p class="purchase-item-price">¥{{ number_format($item->price) }}</p>
          </div>
        </div>
      </div>

      <hr class="purchase-divider">


      <div class="purchase-section">
        <h2 class="purchase-title">支払い方法</h2>

        <select name="payment_method" class="purchase-select" id="payment_method">
          <option value="">選択してください</option>
          <option value="convenience" {{ old('payment_method')==='convenience' ? 'selected' : '' }}>コンビニ払い</option>
          <option value="card" {{ old('payment_method')==='card' ? 'selected' : '' }}>カード支払い</option>
        </select>

        @error('payment_method')
          <p class="error">{{ $message }}</p>
        @enderror
      </div>


      <hr class="purchase-divider">


      <div class="purchase-section">
        <div class="purchase-block-head">
          <h2 class="purchase-title">配送先</h2>
          <a href="/purchase/address/{{ $item->id }}" class="purchase-link">変更する</a>
        </div>

        <div class="purchase-address">
          <p>〒{{ $address['postcode'] ?? '---' }}</p>
          <p>{{ $address['address'] ?? '---' }}</p>
          @if(!empty($address['building']))
            <p>{{ $address['building'] }}</p>
          @endif
        </div>
      </div>

    </section>


    <aside class="purchase-right">
      <div class="purchase-summary">

        <div class="summary-row">
          <span>商品代金</span>
          <span>¥{{ number_format($item->price) }}</span>
        </div>

        <hr class="summary-divider">

        <div class="summary-row">
          <span>支払い方法</span>
          <span id="payment_method_label" class="summary-muted">未選択</span>
        </div>
      </div>

      <div class="purchase-button-wrap">
        @if($item->status === 'sold')
          <button class="purchase-submit is-disabled" type="button" disabled>売り切れ</button>
        @else
          <button class="purchase-submit" type="submit">購入する</button>
        @endif
      </div>
    </aside>

  </form>
</div>

<script>
  const select = document.getElementById('payment_method');
  const label  = document.getElementById('payment_method_label');
  const map = { convenience:'コンビニ支払い', card:'カード支払い'};

  function render(){ label.textContent = map[select.value] ?? '未選択'; }
  select.addEventListener('change', render);
  render();

  

</script>
@endsection