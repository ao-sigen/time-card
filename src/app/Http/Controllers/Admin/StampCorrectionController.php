<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StampCorrection; // ✅ 修正：これを正しく読み込む
use Illuminate\Http\Request;

class StampCorrectionController extends Controller
{
    // 一覧
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = \App\Models\StampCorrection::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.stamp_correction_request.list', compact('requests', 'status'));
    }


    // 詳細
    public function show($id)
    {
        $request = StampCorrection::with(['user', 'attendance'])->findOrFail($id);
        return view('admin.stamp_correction_request.show', compact('request'));
    }

    // 承認
    public function approve(Request $req, $id)
    {
        $request = StampCorrection::findOrFail($id);
        $request->update([
            'status' => 'approved',
            'admin_comment' => $req->comment,
        ]);

        return redirect()->route('admin.stamp_correction_request.list')->with('success', '申請を承認しました。');
    }

    // 却下
    public function reject(Request $req, $id)
    {
        $req->validate(['comment' => 'required|string|max:500']);
        $request = StampCorrection::findOrFail($id); // ✅ 修正：AttendanceCorrection → StampCorrection
        $request->update([
            'status' => 'rejected',
            'admin_comment' => $req->comment,
        ]);

        return redirect()->route('admin.stamp_correction_request.list')->with('error', '申請を却下しました。');
    }
}
