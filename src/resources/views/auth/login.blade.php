<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <header>
        <img src="{{ asset('images/logo.png') }}" alt="ロゴ">
    </header>

    <main>
        <h1>ログイン</h1>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <label for="email">メールアドレス</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <button type="submit" class="login-btn">ログインする</button>
        </form>

        <a href="{{ route('register') }}" class="register-link">会員登録はこちらから</a>
    </main>
</body>
</html>
