<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendanceController extends Controller
{
    public function __construct()
    {
        // 管理者ログイン必須
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        // day=YYYY-MM-DD があればその日、なければ今日
        $day = $request->query('day', Carbon::today('Asia/Tokyo')->toDateString());

        // 指定日の勤怠データを取得
        $attendances = Attendance::with('user')
            ->whereDate('work_date', $day)
            ->get();

        return view('admin.attendance.list', compact('attendances', 'day'));
    }


    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);

        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in'     => $request->clock_in,
            'clock_out'    => $request->clock_out,
            'break_start'  => $request->break_start,
            'break_end'    => $request->break_end,
        ]);

        return redirect()->back()->with('status', '打刻時間を更新しました');
    }

     public function staffAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // クエリパラメータから月を取得（例：2025-10）
        $monthParam = $request->query('month', now()->format('Y-m'));
        $targetMonth = Carbon::createFromFormat('Y-m', $monthParam);

        // 勤怠データ取得（該当月分）
        $attendances = Attendance::where('user_id', $id)
            ->whereMonth('work_date', $targetMonth->month)
            ->whereYear('work_date', $targetMonth->year)
            ->orderBy('work_date', 'asc')
            ->get();

        // 前月・翌月の計算
        $prevMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        return view('admin.attendance.staff', compact('user', 'attendances', 'targetMonth', 'prevMonth', 'nextMonth'));
    }
}
