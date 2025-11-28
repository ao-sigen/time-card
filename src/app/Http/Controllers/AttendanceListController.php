<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceListController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 表示する月（例: 2025-10）
        $month = $request->input('month', Carbon::now('Asia/Tokyo')->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01', 'Asia/Tokyo')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01', 'Asia/Tokyo')->endOfMonth();

        // 月内の日付配列
        $dates = [];
        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            $dates[] = $d->copy();
        }

        // 当月の勤怠データを取得し Y-m-d で keyBy
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->work_date)->format('Y-m-d');
            });

        // 月ラベル・前月・次月
        $targetDate = $startOfMonth;
        $prevDate = $targetDate->copy()->subMonth()->format('Y-m');
        $nextDate = $targetDate->copy()->addMonth()->format('Y-m');

        return view('attendance.list', compact(
            'dates', 'attendances', 'month', 'targetDate', 'prevDate', 'nextDate'
        ));
    }
}

