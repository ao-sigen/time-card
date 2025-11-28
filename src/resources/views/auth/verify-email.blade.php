@extends('layouts.app')

@section('content')
<div class="verify-container">
    <h2>メールアドレスの確認</h2>
    <p>登録したメールアドレス宛に確認メールを送信しました。</p>
    <p>メール内のリンクをクリックして認証を完了してください。</p>

    @if (session('message'))
        <p class="text-green-600">{{ session('message') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-resend">確認メールを再送する</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">ログアウト</button>
    </form>
</div>
@endsection
