@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="create-container">
    <h1 class="page-title">商品の出品</h1>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf


        <div class="form-group">
            <label class="section-label">商品画像</label>

            <div class="image-upload-area">
                <div class="image-preview-box">
                    <img id="image-preview" src="" alt="画像プレビュー" style="display: none;">
                </div>

                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png" hidden>

                <button type="button" class="image-select-btn" onclick="document.getElementById('image').click();">
                    画像を選択する
                </button>
            </div>

            @error('image')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <hr class="section-line">


        <div class="form-group">
            <label class="section-label">カテゴリー</label>

            <div class="category-buttons">
                @foreach($categories as $category)
                    <label class="category-button">
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            class="category-input"
                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                        >
                        <span>{{ $category->category }}</span>
                    </label>
                @endforeach
            </div>

            @error('categories.*')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <div class="form-group">
            <label for="condition_id" class="section-label">商品の状態</label>
            <select name="condition_id" id="condition_id" class="form-input form-select">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                        {{ $condition->condition }}
                    </option>
                @endforeach
            </select>

            @error('condition_id')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <h2 class="section-title">商品名と説明</h2>
        <hr class="section-line">


        <div class="form-group">
            <label for="name" class="section-label">商品名</label>
            <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}">

            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <div class="form-group">
            <label for="brand" class="section-label">ブランド名</label>
            <input type="text" id="brand" name="brand" class="form-input" value="{{ old('brand') }}">

            @error('brand')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <div class="form-group">
            <label for="description" class="section-label">商品の説明</label>
            <textarea id="description" name="description" class="form-input textarea">{{ old('description') }}</textarea>

            @error('description')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <div class="form-group">
            <label for="price" class="section-label">販売価格</label>
            <div class="price-input-wrap">
                <span class="yen-mark">¥</span>
                <input type="text" id="price" name="price" class="form-input price-input" value="{{ old('price') }}">
            </div>

            @error('price')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>


        <div class="submit-area">
            <button type="submit" class="submit-btn">出品する</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');

    if (!imageInput || !preview) return;

    imageInput.addEventListener('change', function (e) {
        const file = e.target.files[0];

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
});
</script>
@endsection