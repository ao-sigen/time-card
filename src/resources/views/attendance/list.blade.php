@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h1>|  勤怠一覧</h1>

    {{-- 現在月を Carbon に変換 --}}
    @php
    use Carbon\Carbon;
    $targetDate = Carbon::parse($month);
    $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
    $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');
    $weekdays = ['日','月','火','水','木','金','土'];
    @endphp

    <div class="date-navigation">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="arrow">← 前月</a>
        <span class="current-date">{{ $targetDate->format('Y年n月') }}</span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="arrow">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>明細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dates as $date)
            @php
            // 勤怠データが存在すれば取得
            $attendance = $attendances->get($date->format('Y-m-d'));
            @endphp
            <tr>
                <td>{{ $date->format('n月j日（' . $weekdays[$date->dayOfWeek] . '）') }}</td>
                <td>
                    {{ $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '—' }}
                </td>
                <td>
                    {{ $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '—' }}
                </td>
                <td>
                    @if($attendance && $attendance->break_start && $attendance->break_end)
                    @php
                    $breakStart = \Carbon\Carbon::parse($attendance->break_start);
                    $breakEnd = \Carbon\Carbon::parse($attendance->break_end);
                    $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                    $breakHours = floor($breakMinutes / 60);
                    $breakRemMinutes = $breakMinutes % 60;
                    @endphp
                    {{ $breakHours }}h{{ $breakRemMinutes }}m
                    @elseif($attendance && $attendance->break_start && !$attendance->break_end)
                    休憩中
                    @else
                    —
                    @endif
                </td>
                <td>
                    @if($attendance && $attendance->clock_in && $attendance->clock_out)
                    @php
                    $start = \Carbon\Carbon::parse($attendance->clock_in);
                    $end = \Carbon\Carbon::parse($attendance->clock_out);

                    // 休憩時間を差し引く
                    $breakMinutes = 0;
                    if ($attendance->break_start && $attendance->break_end) {
                    $breakMinutes = \Carbon\Carbon::parse($attendance->break_start)
                    ->diffInMinutes(\Carbon\Carbon::parse($attendance->break_end));
                    }

                    $totalMinutes = $start->diffInMinutes($end) - $breakMinutes;
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    @endphp
                    {{ $hours }}h{{ $minutes }}m
                    @else
                    —
                    @endif
                </td>
                <td>
                    <a href="{{ route('attendance.detail', ['date' => $date->toDateString()]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection