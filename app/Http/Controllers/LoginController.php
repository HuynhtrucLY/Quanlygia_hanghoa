<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
   public function login(Request $request)
{
    // Lấy dữ liệu từ Form
    $credentials = [
        'ten_dang_nhap' => $request->username, // Tên cột trong DB
        'password' => $request->password,      // Laravel sẽ tự khớp với getAuthPassword() ở trên
        'trang_thai' => 1                      // Chỉ cho phép nếu đang bật
    ];

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/prices');
    }

    return back()->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng!');
}
}