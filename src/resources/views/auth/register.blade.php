<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <header>
        <img src="{{ asset('images/logo.png') }}" alt="ロゴ">
    </header>

    <main>
        <div class="registration-form">
            <h1>会員登録</h1>
            <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf
                <div class="form-group">
                    <label for="name">ユーザー名</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}">
                    @error('name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <p class="error">{{ $message }}</p>
                        @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password">
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">確認用パスワード</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                    @error('password_confirmation')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                    <button type="submit" class="register-btn">登録する</button>
            </form>

                <a href="/login" class="login-link">ログインはこちらから</a>
    </main>
</body>
</html>
