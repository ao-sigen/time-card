@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h2>パスワードをお忘れですか？</h2>
    <p>登録メールアドレスを入力すると、リセット用リンクを送信します。</p>

    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" required autofocus>
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary">リセットリンクを送信</button>
    </form>
</div>
@endsection