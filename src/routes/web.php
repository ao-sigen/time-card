<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceDetailController as AdminAttendanceDetailController;
use App\Http\Controllers\Admin\StampCorrectionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceCorrectionRequestController;
use Laravel\Fortify\Fortify;

Route::get('/', function () {
    return view('auth.login');
})->name('home');
// Fortifyの登録画面ルートを有効化
Fortify::registerView(function () {
    return view('auth.register');
});

// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

// ログイン
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {

    // 管理者ログイン
    Route::get('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

    // 認証後管理者機能
    Route::middleware('auth:admin')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('attendance.list');
        Route::get('/attendance/detail/{id}', [AdminAttendanceDetailController::class, 'show'])->name('attendance.detail');
        Route::post('/attendance/detail/{id}', [AdminAttendanceDetailController::class, 'update'])->name('attendance.update');
        Route::get('/stamp_correction_request/list', [StampCorrectionController::class, 'index'])->name('stamp_correction_request.list');
        Route::post('/stamp_correction_request/approve/{id}', [StampCorrectionController::class, 'approve'])->name('stamp_correction_request.approve');
        Route::post('/stamp_correction_request/reject/{id}', [StampCorrectionController::class, 'reject'])->name('stamp_correction_request.reject');
        // 管理者：修正申請一覧
        Route::get('/admin/stamp_correction_request/list', [App\Http\Controllers\Admin\StampCorrectionController::class, 'index'])
            ->name('admin.stamp_correction_request.list');

        // ✅ 管理者：修正申請一覧
        Route::get('/stamp_correction_request/list', [StampCorrectionController::class, 'index'])
            ->name('stamp_correction_request.list');

        // ✅ 管理者：修正申請詳細（承認画面）
        Route::get('/stamp_correction_request/show/{id}', [StampCorrectionController::class, 'show'])
            ->name('stamp_correction_request.show');

        // ✅ 管理者：承認処理
        Route::post('/stamp_correction_request/approve/{id}', [StampCorrectionController::class, 'approve'])
            ->name('stamp_correction_request.approve');

        // ✅ 管理者：却下処理
        Route::post('/stamp_correction_request/reject/{id}', [StampCorrectionController::class, 'reject'])
            ->name('stamp_correction_request.reject');
        Route::get('/admin/attendance/staff/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');
        // 管理者：スタッフ別勤怠一覧（月指定対応）
        Route::get('/admin/attendance/staff/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');


    });
});


// 勤怠関連
Route::middleware(['auth:web'])->group(function () {
    // 明細ルート（ID）
    Route::get('/attendance/detail/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/detail/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    // 一覧・登録
    Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // 修正申請一覧
    Route::get('/correction_request/list', [AttendanceCorrectionRequestController::class, 'list'])
        ->name('correction_request.list');

    // 打刻
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.breakStart');
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.breakEnd');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');

    // 勤怠トップ
    Route::get('/attendance', [AttendanceController::class, 'punch'])->name('attendance.index');

    // ⭐⭐⭐ ここを一番下に置く（重要） ⭐⭐⭐
    // 日付で詳細を開く場合のルート
    Route::get('/attendance/detail/{date?}', [AttendanceDetailController::class, 'show'])->name('attendance.detail');

    // 修正申請の保存は AttendanceCorrectionRequestController に
    Route::post(
        '/stamp_correction_request/store',
        [AttendanceCorrectionRequestController::class, 'store']
    )->name('stamp_correction_request.store');
    Route::post('/attendance/detail', [AttendanceDetailController::class, 'update'])->name('stamp_correction_request.store');
});



// メール確認待ち画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証リンクをクリックしたとき
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールを再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

