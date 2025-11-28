@extends('layouts.admin.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-attendance-staff.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    <h1>| {{ $user->name }}さんの勤怠一覧（{{ $targetMonth->format('Y年m月') }}）</h1>

    <div class="date-navigation">
        <a class="arrow" href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $prevMonth]) }}">← 前月</a>

        <span>{{ $targetMonth->format('Y年m月') }}</span>

        <a class="arrow" href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $nextMonth]) }}">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>勤務時間</th>
                <th>明細</th>
            </tr>
        </thead>

        <tbody>
            @php
            $start = $targetMonth->copy()->startOfMonth();
            $end = $targetMonth->copy()->endOfMonth();
            @endphp

            @for ($date = $start->copy(); $date->lte($end); $date->addDay())
            @php
            $attendanceRecord = $attendances->firstWhere('work_date', $date->format('Y-m-d'));
            @endphp

            <tr>
                <td>{{ $date->format('Y-m-d') }}</td>

                <td>{{ $attendanceRecord->clock_in ?? '-' }}</td>
                <td>{{ $attendanceRecord->clock_out ?? '-' }}</td>

                <td>
                    @if ($attendanceRecord && $attendanceRecord->clock_in && $attendanceRecord->clock_out)
                    @php
                    $start = \Carbon\Carbon::parse($attendanceRecord->clock_in);
                    $end = \Carbon\Carbon::parse($attendanceRecord->clock_out);

                    // 休憩時間がある場合は引く
                    $breakMinutes = 0;
                    if ($attendanceRecord->break_start && $attendanceRecord->break_end) {
                    $breakStart = \Carbon\Carbon::parse($attendanceRecord->break_start);
                    $breakEnd = \Carbon\Carbon::parse($attendanceRecord->break_end);
                    $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                    }

                    $totalMinutes = $end->diffInMinutes($start) - $breakMinutes;
                    $hours = intdiv($totalMinutes, 60);
                    $minutes = $totalMinutes % 60;
                    @endphp
                    {{ $hours }}時間{{ $minutes }}分
                    @else
                    -
                    @endif
                </td>

                <td>
                    @if ($attendanceRecord)
                    <a href="{{ route('admin.attendance.detail', ['id' => $attendanceRecord->id]) }}" class="detail-link">明細</a>
                    @else
                    {{-- 打刻がない場合は日付だけで明細画面へ遷移 --}}
                    <a href="{{ route('admin.attendance.detail', ['id' => 0, 'date' => $date->toDateString(), 'user_id' => $user->id]) }}" class="detail-link">明細</a>
                    @endif
                </td>

            </tr>
            @endfor
        </tbody>
    </table>

    <div class="back-link">
        <a href="{{ route('admin.staff.list') }}">← スタッフ一覧に戻る</a>
    </div>

</div>
@endsection