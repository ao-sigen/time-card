<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-img">
            </a>

            <nav class="header__nav">
                <a href="{{ route('attendance.create') }}" class="btn btn-primary">勤怠登録</a>
                <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('correction_request.list') }}">申請一覧</a>

                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout">ログアウト</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>


</html>