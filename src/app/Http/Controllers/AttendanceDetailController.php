<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceDetailController extends Controller
{
    public function show($date = null)
    {
        $user = Auth::user();

        $targetDate = $date ? Carbon::parse($date, 'Asia/Tokyo') : Carbon::today('Asia/Tokyo');
        $prevDate = $targetDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $targetDate->copy()->addDay()->format('Y-m-d');

        // attendance がなければ空のモデルを作る
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'work_date' => $targetDate->toDateString(),
        ]);

        // 既存申請のチェック
        $existingRequest = $attendance->exists
            ? AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first()
            : null;

        return view('attendance.detail', compact(
            'attendance',
            'user',
            'targetDate',
            'prevDate',
            'nextDate',
            'existingRequest'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'attendance_id' => 'nullable|exists:attendances,id',
            'work_date' => 'required|date',
            'requested_clock_in' => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',
            'requested_break_start1' => 'nullable|date_format:H:i',
            'requested_break_end1'   => 'nullable|date_format:H:i',
            'requested_break_start2' => 'nullable|date_format:H:i',
            'requested_break_end2'   => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:500',
        ]);

        // attendance を取得、なければ作る
        $attendance = $request->attendance_id
            ? Attendance::findOrFail($request->attendance_id)
            : Attendance::firstOrCreate([
                'user_id' => $user->id,
                'work_date' => $request->work_date,
            ]);

        // 既存申請があるか確認
        $existingRequest = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', '承認待ちのため修正できません。');
        }

        // 修正申請を作成
        AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'requested_break_start1' => $request->requested_break_start1,
            'requested_break_end1' => $request->requested_break_end1,
            'requested_break_start2' => $request->requested_break_start2,
            'requested_break_end2' => $request->requested_break_end2,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', '修正申請を送信しました。管理者の承認をお待ちください。');
    }
}
