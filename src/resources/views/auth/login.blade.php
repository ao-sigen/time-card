<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/login">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-img">
            </a>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>ログイン</h2>
            @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label>メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label>パスワード</label>
                    <input type="password" name="password">
                    @error('password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit">ログイン</button>
            </form>

            <p><a href="{{ route('register') }}">アカウント作成はこちら</a></p>
        </div>
    </main>
</body>

</html>