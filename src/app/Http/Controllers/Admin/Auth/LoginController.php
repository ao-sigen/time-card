<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }
    protected function redirectTo()
    {
        return '/admin/attendance/list';
    }
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.attendance.list');
    }



    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return redirect()->route('admin.attendance.list');
        } else {
            return back()->withErrors([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ]);
        }

    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
