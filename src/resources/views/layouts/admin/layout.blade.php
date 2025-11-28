<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠管理アプリ（管理者）</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <!-- 管理者ヘッダー -->
    <header class="header">
        <div class="header__inner">
            <!-- ロゴ -->
            <a href="{{ route('admin.attendance.list') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo-img">
            </a>

            <!-- ナビゲーション -->
            <nav class="header__nav">
                <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                <a href="{{ route('admin.stamp_correction_request.list') }}">申請一覧</a>

                <!-- ログアウトボタン -->
                <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">ログアウト</button>
                </form>
            </nav>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <main>
        @yield('content')
    </main>
</body>

</html>