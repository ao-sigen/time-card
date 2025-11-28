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
            <a class="header__logo" href="/admin/login">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-img">
            </a>
        </div>
    </header>

    <main>
        <div class="login-form">
            <h2>管理者ログイン</h2>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div>
                    <label>メールアドレス</label>
                    <input type="email" name="email" required>
                </div>

                <div>
                    <label>パスワード</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit">ログイン</button>

                @error('email')
                <p class="error">{{ $message }}</p>
                @enderror
            </form>
        </div>
    </main>

</body>

</html>