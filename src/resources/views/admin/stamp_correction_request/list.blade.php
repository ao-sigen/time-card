@extends('layouts.admin.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-correction-request.css') }}">
@endsection

@section('content')

<div class="correction-list-container">

    <h2>申請一覧</h2>

    {{-- タブ切り替え --}}
    <div class="tab-buttons">
        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'pending']) }}"
            class="tab {{ ($status ?? 'pending') === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'approved']) }}"
            class="tab {{ ($status ?? '') === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>


    {{-- データテーブル --}}
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
            @forelse($requests as $request)
            <tr>
                <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                <td>{{ $request->user->name ?? '不明' }}</td>
                <td>{{ $request->attendance->work_date ?? '---' }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('admin.stamp_correction_request.show', $request->id) }}"
                        class="btn-detail">
                        明細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="no-data">申請データがありません。</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $requests->links() }}
    </div>

</div>
@endsection