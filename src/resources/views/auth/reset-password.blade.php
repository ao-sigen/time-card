@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h2>新しいパスワードを設定</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">新しいパスワード</label>
            <input id="password" type="password" name="password" required>
            @error('password')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn-primary">パスワードを更新</button>
    </form>
</div>
@endsection