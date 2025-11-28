@extends('layouts.admin.layout') {{-- 管理者用レイアウト --}}

@section('content')
<div class="attendance-detail-container">
    <h2>{{ $attendance->user->name }} の打刻明細</h2>
    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf

        <div class="form-group">
            <label>出勤時間</label>
            <input type="time" name="clock_in" value="{{ $attendance->clock_in ?? '' }}">
        </div>

        <div class="form-group">
            <label>退勤時間</label>
            <input type="time" name="clock_out" value="{{ $attendance->clock_out ?? '' }}">
        </div>

        <div class="form-group">
            <label>休憩入り</label>
            <input type="time" name="break_start" value="{{ $attendance->break_start ?? '' }}">
        </div>

        <div class="form-group">
            <label>休憩戻り</label>
            <input type="time" name="break_end" value="{{ $attendance->break_end ?? '' }}">
        </div>

        <button type="submit">更新</button>
    </form>
</div>
@endsection
