@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="attendance-title">勤怠明細</h2>

    {{-- 名前 --}}
    <div class="attendance-item">
        <label>名前：</label>
        <span>{{ $attendance->user->name ?? auth()->user()->name ?? '不明' }}</span>
    </div>

    {{-- 日付 --}}
    <div class="attendance-item">
        <label>日付：</label>
        <span>{{ $targetDate->format('Y年m月d日') }}</span>
    </div>

    {{-- 勤怠修正フォーム --}}
    <form action="{{ route('stamp_correction_request.store') }}" method="POST">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <input type="hidden" name="work_date" value="{{ $targetDate->toDateString() }}">

        <div class="attendance-field">
            <label>出勤・退勤：</label>
            <input type="time" name="requested_clock_in" value="{{ old('requested_clock_in', $attendance->clock_in ?? '') }}">
            <span>～</span>
            <input type="time" name="requested_clock_out" value="{{ old('requested_clock_out', $attendance->clock_out ?? '') }}">
            @error('requested_clock_in')<div class="error-message">{{ $message }}</div>@enderror
            @error('requested_clock_out')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>休憩1：</label>
            <input type="time" name="requested_break_start1" value="{{ old('requested_break_start1', $attendance->break_start1 ?? '') }}">
            <span>～</span>
            <input type="time" name="requested_break_end1" value="{{ old('requested_break_end1', $attendance->break_end1 ?? '') }}">
            @error('requested_break_start1')<div class="error-message">{{ $message }}</div>@enderror
            @error('requested_break_end1')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>休憩2：</label>
            <input type="time" name="requested_break_start2" value="{{ old('requested_break_start2', $attendance->break_start2 ?? '') }}">
            <span>～</span>
            <input type="time" name="requested_break_end2" value="{{ old('requested_break_end2', $attendance->break_end2 ?? '') }}">
            @error('requested_break_start2')<div class="error-message">{{ $message }}</div>@enderror
            @error('requested_break_end2')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-field">
            <label>理由：</label>
            <textarea name="reason">{{ old('reason') }}</textarea>
            @error('reason')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="attendance-buttons">
            @if($existingRequest)
            <p>承認待ちのため修正できません</p>
            @else
            <button type="submit" class="btn-primary">申請</button>
            @endif
        </div>
    </form>
</div>
@endsection