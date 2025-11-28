@extends('layouts.admin.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-request-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="attendance-title">勤怠明細</h2>

    {{-- スタッフ名 --}}
    <div class="attendance-item">
        <label>スタッフ名</label>
        <span>{{ $request->user->name ?? '不明' }}</span>
    </div>

    {{-- 日付 --}}
    <div class="attendance-item">
        <label>日付</label>
        <span>
            @if($request->attendance && $request->attendance->work_date)
            {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y年 n月 j日') }}
            @elseif($request->work_date)
            {{ \Carbon\Carbon::parse($request->work_date)->format('Y年 n月 j日') }}
            @else
            -
            @endif
        </span>
    </div>


    {{-- 出勤〜退勤 --}}
    <div class="attendance-field">
        <label>出勤・退勤</label>
        <span>{{ $request->requested_clock_in ?? '-' }}</span>
        <span class="tilde">～</span>
        <span>{{ $request->requested_clock_out ?? '-' }}</span>
    </div>


    {{-- 休憩1 --}}
    <div class="attendance-field">
        <label>休憩1</label>
        <span>{{ $request->requested_break_start1 ?? '-' }}</span>
        <span class="tilde">～</span>
        <span>{{ $request->requested_break_end1 ?? '-' }}</span>
    </div>

    {{-- 休憩2 --}}
    <div class="attendance-field">
        <label>休憩2</label>
        <span>{{ $request->requested_break_start2 ?? '-' }}</span>
        <span class="tilde">～</span>
        <span>{{ $request->requested_break_end2 ?? '-' }}</span>
    </div>

    {{-- 理由 --}}
    <div class="attendance-field">
        <label>理由</label>
        <span>{{ $request->reason ?? '-' }}</span>
    </div>

    {{-- 承認フォーム --}}
    @if($request->status === 'pending')
    <form method="POST" action="{{ route('admin.stamp_correction_request.approve', $request->id) }}" class="attendance-buttons">
        @csrf
        <button type="submit" class="btn-primary">承認する</button>
    </form>
    @else
    <div class="attendance-buttons">
        <p>この申請はすでに処理済みです。</p>
    </div>
    @endif
</div>
@endsection