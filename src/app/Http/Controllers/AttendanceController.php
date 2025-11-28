<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * 勤怠画面表示
     */
    public function index(Request $request)
    {
        $user = Auth::user();
    $today = Carbon::now('Asia/Tokyo')->toDateString();

    // 今日の勤怠データ取得
    $attendance = Attendance::where('user_id', $user->id)
        ->where('work_date', $today)
        ->first();

    // 勤務状態を判定
    if (!$attendance || !$attendance->clock_in) {
        $status = '勤務外';
        $buttonsEnabled = true;
    } elseif ($attendance->clock_in && !$attendance->break_start && !$attendance->clock_out) {
        $status = '勤務中';
        $buttonsEnabled = true;
    } elseif ($attendance->break_start && !$attendance->break_end) {
        $status = '休憩中';
        $buttonsEnabled = true;
    } elseif ($attendance->clock_out) {
        $status = '勤務終了';
        $buttonsEnabled = false;
    } else {
        $status = '勤務中';
        $buttonsEnabled = true;
    }

    return view('attendance.index', compact('today', 'status', 'buttonsEnabled', 'attendance'));
    }

    public function punch(Request $request)
    {
        $user = Auth::user();

        // 表示する月 (例: 2025-10)
        $month = $request->input('month', Carbon::now('Asia/Tokyo')->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01', 'Asia/Tokyo')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01', 'Asia/Tokyo')->endOfMonth();

        // 月内の日付配列
        $dates = [];
        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $dates[] = $d->copy();
        }

        // 当月のそのユーザーの勤怠を取得して work_date をキーにする
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                // key を 'Y-m-d' にする（work_date が date 型ならそのまま）
                return Carbon::parse($item->work_date)->format('Y-m-d');
            });

        // 表示用の月ラベル・前月・次月
        $targetDate = Carbon::parse($month . '-01', 'Asia/Tokyo');
        $prevDate = $targetDate->copy()->subMonth()->format('Y-m');
        $nextDate = $targetDate->copy()->addMonth()->format('Y-m');
        return view('attendance.list', compact(
            'dates', 'attendances', 'month', 'targetDate', 'prevDate', 'nextDate'
        ));
    }

    public function list(Request $request)
    {
        $user = auth()->user();

        // ① 現在の月を取得（リクエストがあればその月）
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // ② 月の開始日と終了日を取得
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // ③ 1日ずつループして月内の日付を配列に格納
        $dates = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->copy();
        }

        // ④ 該当月の勤怠データを取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('work_date');

        return view('attendance.list', compact('dates', 'attendances', 'month'));
    }

    public function detail($id)
    {
        // 勤怠データを取得
        $attendance = Attendance::find($id);
        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'work_date' => $targetDate->toDateString()
            ],
            [
                'clock_in' => null,
                'clock_out' => null,
            ]
        );


        if (!$attendance) {
            abort(404, '勤怠データが見つかりません');
        }

        $user = Auth::user();

        // 日付操作
        $targetDate = Carbon::parse($attendance->work_date);
        $prevDate = $targetDate->copy()->subDay();
        $nextDate = $targetDate->copy()->addDay();

        // 合計勤務時間を計算
        $totalHours = '-';
        if ($attendance->clock_in && $attendance->clock_out) {
            $start = Carbon::parse($attendance->clock_in);
            $end = Carbon::parse($attendance->clock_out);

            $breakMinutes = 0;
            if ($attendance->break_start && $attendance->break_end) {
                $breakMinutes = Carbon::parse($attendance->break_end)
                    ->diffInMinutes(Carbon::parse($attendance->break_start));
            }

            $workMinutes = max(0, $end->diffInMinutes($start) - $breakMinutes);
            $hours = intdiv($workMinutes, 60);
            $minutes = $workMinutes % 60;
            $totalHours = "{$hours}h {$minutes}m";
        }

        // 👇ここが重要
        return view('attendance.detail', compact(
            'attendance',
            'user',
            'targetDate',
            'prevDate',
            'nextDate',
            'totalHours'
        ));
    }



    /**
     * 出勤処理
     */
    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Tokyo')->toDateString();

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['clock_in' => Carbon::now('Asia/Tokyo')->toTimeString()]
        );

        return redirect()->back()->with('status', '出勤打刻しました');
    }

    /**
     * 休憩開始
     */
    public function breakStart()
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Tokyo')->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && !$attendance->break_start) {
            $attendance->update(['break_start' => Carbon::now('Asia/Tokyo')->toTimeString()]);
        }

        return redirect()->back()->with('status', '休憩開始しました');
    }

    /**
     * 休憩終了
     */
    public function breakEnd()
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Tokyo')->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->break_start && !$attendance->break_end) {
            $attendance->update(['break_end' => Carbon::now('Asia/Tokyo')->toTimeString()]);
        }

        return redirect()->back()->with('status', '休憩終了しました');
    }

    /**
     * 退勤処理
     */
    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today('Asia/Tokyo')->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update(['clock_out' => Carbon::now('Asia/Tokyo')->toTimeString()]);
        }

        return redirect()->back()->with('status', '退勤打刻しました');
    }

    public function show($date = null)
    {
        $user = Auth::user();
        // 指定がなければ今日の日付
        $targetDate = $date ? Carbon::parse($date, 'Asia/Tokyo') : Carbon::today('Asia/Tokyo');
        $today = Carbon::today('Asia/Tokyo');

        // 前日・翌日
        $prevDate = $targetDate->copy()->subDay()->toDateString();
        $nextDate = $targetDate->copy()->addDay()->toDateString();

        // 勤怠データ取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $targetDate->toDateString())
            ->first();

        // 合計勤務時間（休憩を除外）
        $totalHours = null;
        if ($attendance && $attendance->clock_in && $attendance->clock_out) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($attendance->clock_out);
            $breakMinutes = 0;

            if ($attendance->break_start && $attendance->break_end) {
                $breakStart = Carbon::parse($attendance->break_start);
                $breakEnd = Carbon::parse($attendance->break_end);
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            }

            $totalMinutes = $clockOut->diffInMinutes($clockIn) - $breakMinutes;
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $totalHours = sprintf("%02d:%02d", $hours, $minutes);
        }

        return view('attendance.detail', compact(
            'attendance', 'user', 'targetDate', 'today', 'prevDate', 'nextDate', 'totalHours'
        ));
    }

    public function create()
    {
        $user = Auth::user();
    $today = Carbon::now('Asia/Tokyo')->toDateString();

    // 今日の勤怠データ取得
    $attendance = Attendance::where('user_id', $user->id)
        ->where('work_date', $today)
        ->first();

    // 勤務状態を判定
    if (!$attendance || !$attendance->clock_in) {
        $status = '勤務外';
        $buttonsEnabled = true;
    } elseif ($attendance->clock_in && !$attendance->break_start && !$attendance->clock_out) {
        $status = '勤務中';
        $buttonsEnabled = true;
    } elseif ($attendance->break_start && !$attendance->break_end) {
        $status = '休憩中';
        $buttonsEnabled = true;
    } elseif ($attendance->clock_out) {
        $status = '勤務終了';
        $buttonsEnabled = false;
    } else {
        $status = '勤務中';
        $buttonsEnabled = true;
    }

    return view('attendance.index', compact('today', 'status', 'buttonsEnabled', 'attendance'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
        ]);

        $user = Auth::user();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => Carbon::today('Asia/Tokyo'),
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
        ]);

        return redirect()->route('attendance.list')->with('status', '勤怠登録しました');
    }
    
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $date = Carbon::parse($attendance->work_date);
        return view('attendance.edit', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'break_start1' => $request->break_start1,
            'break_end1' => $request->break_end1,
            'break_start2' => $request->break_start2,
            'break_end2' => $request->break_end2,
            'reason' => $request->reason,
        ]);

        return redirect()->route('attendance.list')->with('success', '勤怠を更新しました。');
    }
}
