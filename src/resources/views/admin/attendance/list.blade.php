@extends('layouts.admin.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-attendance.css') }}">
@endsection

@section('content')
<div class="admin-attendance-container">

    @php
    use Carbon\Carbon;

    $targetDate = isset($day) ? Carbon::parse($day) : Carbon::parse($today);
    $prevDay = $targetDate->copy()->subDay()->toDateString();
    $nextDay = $targetDate->copy()->addDay()->toDateString();
    @endphp

    <h2>|  {{ $targetDate->format('Y年n月d日') }}の勤怠</h2>

    {{-- 日付移動ナビ --}}
    <div class="attendance-nav">
        <a href="{{ route('admin.attendance.list', ['day' => $prevDay]) }}" class="nav-btn">← 前日</a>
        <span class="nav-date">{{ $targetDate->format('Y年n月d日') }}</span>
        <a href="{{ route('admin.attendance.list', ['day' => $nextDay]) }}" class="nav-btn">翌日 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            @php
            $total = '-';
            $breakTime = '-';
            if ($attendance->clock_in && $attendance->clock_out) {
            $start = \Carbon\Carbon::parse($attendance->clock_in, 'Asia/Tokyo');
            $end = \Carbon\Carbon::parse($attendance->clock_out, 'Asia/Tokyo');

            if ($attendance->break_start && $attendance->break_end) {
            $breakStart = \Carbon\Carbon::parse($attendance->break_start, 'Asia/Tokyo');
            $breakEnd = \Carbon\Carbon::parse($attendance->break_end, 'Asia/Tokyo');
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $breakTime = $breakStart->format('H:i') . '～' . $breakEnd->format('H:i');
            }

            $totalMinutes = $end->diffInMinutes($start) - ($breakMinutes ?? 0);
            $hours = intdiv($totalMinutes, 60);
            $minutes = $totalMinutes % 60;
            $total = "{$hours}時間{$minutes}分";
            }
            @endphp

            <tr>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                <td>{{ $breakTime }}</td>
                <td>{{ $total }}</td>
                <td>
                    <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}" class="btn-detail">明細</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;">本日の出勤者はいません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection