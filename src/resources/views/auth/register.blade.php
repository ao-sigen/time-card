<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-img">
            </a>
        </div>
    </header>

    <main>
        <div class="register-form">
            <h2>会員登録</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div>
                    <label>名前</label>
                    <input type="text" name="name" value="{{ old('name') }}">
                    @error('name')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>

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

                <div>
                    <label>パスワード確認</label>
                    <input type="password" name="password_confirmation">
                </div>

                <button type="submit">登録</button>
            </form>
            <p><a href="{{ route('login') }}">ログインはこちら</a></p>
        </div>
    </main>
</body>

</html>