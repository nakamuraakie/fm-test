<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>

    <header class="header">
        <div class="header-inner">


            <div class="header-left">
                    <img src="{{ asset('images/logo.png') }}" alt="ロゴ" class="logo">
                </a>
            </div>


            <div class="header-center">
                <form method="GET" action="{{ route('index') }}">
                    <input
                        type="text"
                        name="keyword"
                        value="{{ request('keyword') }}"
                        placeholder="なにをお探しですか？"
                        class="search-input"
                    >
                    <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
                </form>
            </div>


            <div class="header-right">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="header-link logout-button">ログアウト</button>
                </form>

                <a href="{{ route('mypage') }}" class="header-link">マイページ</a>


                <a href="{{ auth()->check() ? route('items.create') : route('login') }}" class="sell-button">
                    出品
                </a>
            </div>

        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>