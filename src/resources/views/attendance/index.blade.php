@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h2>勤怠登録</h2>

    <!-- 勤務状態 -->
    <p><strong>{{ $status }}</strong></p>

    <!-- 日付表示 -->
    <p>
        @php
        $weekdays = ['日','月','火','水','木','金','土'];
        $d = \Carbon\Carbon::parse($today);
        $dateLabel = $d->format('Y年n月j日') . '（' . $weekdays[$d->dayOfWeek] . '）';
        @endphp
        {{ $dateLabel }}
    </p>



    <p>
        {{ \Carbon\Carbon::now('Asia/Tokyo')->format('H:i:s') }}
    </p>

    <!-- ボタン -->
    @if($buttonsEnabled)
        @if($status === '勤務外')
        <form method="POST" action="{{ route('attendance.clockIn') }}">
            @csrf
            <button type="submit" class="btn btn-black">出勤</button>
        </form>
        @elseif($status === '勤務中')
        <form method="POST" action="{{ route('attendance.breakStart') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-white">休憩入り</button>
        </form>

        <form method="POST" action="{{ route('attendance.clockOut') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-black">退勤</button>
        </form>
        @elseif($status === '休憩中')
        <form method="POST" action="{{ route('attendance.breakEnd') }}">
            @csrf
            <button type="submit" class="btn btn-white">休憩戻り</button>
        </form>
    @endif
    @else
    <p>お疲れさまでした</p>
    @endif

</div>
@endsection