@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/correction-list.css') }}">
@endsection

@section('content')
<div class="correction-list-container">
    <h2>修正申請一覧</h2>

    {{-- ステータス切り替えタブ --}}
    <div class="tab-buttons">
        <a href="{{ route('correction_request.list', ['status' => 'pending']) }}"
           class="tab {{ $status === 'pending' ? 'active' : '' }}">
           承認待ち
        </a>
        <a href="{{ route('correction_request.list', ['status' => 'approved']) }}"
           class="tab {{ $status === 'approved' ? 'active' : '' }}">
           承認済み
        </a>
    </div>

    @if ($requests->isEmpty())
        <p class="no-data">現在、{{ $status === 'pending' ? '承認待ち' : '承認済み' }}の申請はありません。</p>
    @else
    <table class="correction-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>理由</th>
                <th>申請日時</th>
                <th>明細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ optional($request->attendance)->work_date ? \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') : '-' }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>
                        {{ optional($request->attendance)->work_date
                            ? \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d')
                            : '-' }}
                    </td>

                    <td>
                        <a href="{{ route('attendance.detail', ['id' => $request->attendance_id]) }}" class="btn-detail">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $requests->links() }}
    @endif
</div>
@endsection
