<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceCorrectionRequest;

class AttendanceCorrectionRequestController extends Controller
{
    public function store(AttendanceCorrectionRequest $request)
    {
        $data = $request->validated();

        AttendanceCorrectionRequest::create([
            'attendance_id' => $data['attendance_id'],
            'user_id' => auth()->id(),
            'requested_clock_in' => $data['requested_clock_in'] ?? null,
            'requested_clock_out' => $data['requested_clock_out'] ?? null,
            'requested_break_start1' => $data['requested_break_start1'] ?? null,
            'requested_break_end1' => $data['requested_break_end1'] ?? null,
            'requested_break_start2' => $data['requested_break_start2'] ?? null,
            'requested_break_end2' => $data['requested_break_end2'] ?? null,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', '修正申請を送信しました。管理者の承認をお待ちください。');
    }



    public function list(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceCorrectionRequest::with('attendance', 'user') // ← ここはモデルの方
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('attendance.correction_request_list', [
            'requests' => $requests,
            'status' => $status,
        ]);
    }

}
