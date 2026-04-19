<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // ================= LOGIN =================
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'ten_dang_nhap' => 'required',
            'mat_khau' => 'required'
        ]);

        $credentials = [
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'password'      => $request->mat_khau,
            'trang_thai'    => 1 // 🔥 chỉ login khi đã duyệt
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->with('error', 'Tài khoản chưa được duyệt hoặc sai thông tin!');
    }

    // ================= REGISTER =================
    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
{
    $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:6|confirmed',
], [
    'email.required' => 'Email không được để trống',
    'email.email' => 'Email không hợp lệ',
    'password.required' => 'Mật khẩu không được để trống',
    'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
    'password.confirmed' => 'Xác nhận mật khẩu không khớp',
]);

    // 🔥 tạo user
    $user = User::create([
        'ho_ten' => $request->ho_ten,
        'ten_dang_nhap' => $request->ten_dang_nhap,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'vai_tro' => 'nhan_vien',
        'trang_thai' => 0
    ]);

    // 🔥 gửi mail
    try {
    Mail::raw(
        "Có user mới đăng ký:\n\nTên: {$user->ho_ten}\nEmail: {$user->email}",
        function ($message) {
            $message->to('ly_dth225694@students.agu.edu.vn')
                    ->subject('User mới đăng ký');
        }
    );
} catch (\Exception $e) {
    // bỏ qua lỗi mail để không ảnh hưởng đăng ký
}

    // ✅ QUAN TRỌNG NHẤT (THÊM Ở ĐÂY)
    return redirect('/login')->with('success', 'Đăng ký thành công! Chờ admin duyệt.');
}

    // ================= LOGOUT =================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ================= PROFILE =================
    public function profile()
    {
        return view('profile');
    }
public function changePassword(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', '❌ Mật khẩu hiện tại không đúng!');
    }
$request->validate([
    'password' => 'required|confirmed',
], [
    'password.confirmed' => '❌ Xác nhận mật khẩu không khớp!',
    'password.required' => '❌ Vui lòng nhập mật khẩu!',
]);
    
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    return back()->with('success', '✅ Đổi mật khẩu thành công!');
}
    // ================= ADMIN DUYỆT =================
   public function approve($id)
{
    // 🔍 lấy user
    $user = User::where('ma_nguoi_dung', $id)->first();

    // ✅ duyệt tài khoản
    User::where('ma_nguoi_dung', $id)->update([
        'trang_thai' => 1
    ]);

    // 📩 gửi mail cho user
    if ($user && $user->email) {
        Mail::raw(
            "Chào {$user->ho_ten},\n\nTài khoản của bạn đã được admin duyệt. Bạn có thể đăng nhập hệ thống.",
            function ($msg) use ($user) {
                $msg->to($user->email)
                    ->subject('✔ Tài khoản đã được duyệt');
            }
        );
    }

    return back()->with('success', '✔ Đã duyệt tài khoản!');
}
}