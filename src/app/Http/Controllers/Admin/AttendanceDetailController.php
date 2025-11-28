<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceDetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);

        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());

        return redirect()->route('admin.attendance.detail', ['id' => $id])
                         ->with('success', '勤怠情報を更新しました。');
    }
}
