<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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
        'mat_khau' => 'required',
        'g-recaptcha-response' => 'required'
    ], [
        'g-recaptcha-response.required' => ' Vui lòng xác minh CAPTCHA!'
    ]);

    // CAPTCHA check
    $response = \Illuminate\Support\Facades\Http::asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]
    );

    $result = $response->json();

    if (!isset($result['success']) || $result['success'] != true) {
        return back()->with('error', 'CAPTCHA không hợp lệ!');
    }

    // 1. kiểm tra user tồn tại
    $user = \App\Models\User::where('ten_dang_nhap', $request->ten_dang_nhap)->first();

    if (!$user) {
        return back()->with('error', 'Sai tên đăng nhập!');
    }

    // 2. kiểm tra mật khẩu
    if (!\Illuminate\Support\Facades\Hash::check($request->mat_khau, $user->password)) {
        return back()->with('error', 'Sai mật khẩu!');
    }

    // 3. kiểm tra trạng thái
    if ($user->trang_thai != 1) {
        return back()->with('error', 'Tài khoản chưa được duyệt!');
    }

    // login thủ công
    Auth::login($user);
    $request->session()->regenerate();

    return redirect('/dashboard');
    }

    // ================= REGISTER =================
    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
    $request->validate([
        'ho_ten' => 'required',
        'ten_dang_nhap' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
    ], [
        'ho_ten.required' => 'Vui lòng nhập họ tên',

        'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập',
        'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại',

        'email.required' => 'Vui lòng nhập email',
        'email.email' => 'Email không hợp lệ',
        'email.unique' => 'Email đã được sử dụng',

        'password.required' => 'Vui lòng nhập mật khẩu',
        'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên',
        'password.confirmed' => 'Xác nhận mật khẩu không khớp',
    ]);

    $user = User::create([
        'ho_ten' => $request->ho_ten,
        'ten_dang_nhap' => $request->ten_dang_nhap,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'vai_tro' => 'nhan_vien',
        'trang_thai' => 0
    ]);

    try {
        Mail::raw(
            "THÔNG BÁO TỪ HỆ THỐNG QUẢN LÝ GIÁ\n\n".
            "User mới đăng ký:\nTên: {$user->ho_ten}\nEmail: {$user->email}",
            function ($message) {
                $message->to('ly_dth225694@students.agu.edu.vn')
                        ->subject('User mới đăng ký');
            }
        );
    } catch (\Exception $e) {}

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

    // ================= CHANGE PASSWORD =================
   public function changePassword(Request $request)
    {
    // validate mật khẩu hiện tại
    $request->validate([
        'current_password' => 'required',
    ], [
        'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
    ]);

    // lấy user
    $user = User::find(Auth::id());

    if (!$user) {
        return back()->with('error', 'Không tìm thấy người dùng!');
    }

    // check mật khẩu hiện tại
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', ' Mật khẩu hiện tại không đúng!');
    }

    // validate mật khẩu mới (ĐẦY ĐỦ LUÔN)
    $request->validate([
        'password' => 'required|min:6|confirmed',
    ], [
        'password.required' => 'Vui lòng nhập mật khẩu mới',
        'password.min' => 'Mật khẩu mới phải từ 6 ký tự trở lên',
        'password.confirmed' => 'Xác nhận mật khẩu không khớp',
    ]);

    // update password
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    return back()->with('success', ' Đổi mật khẩu thành công!');
    }
    // ================= ADMIN DUYỆT =================
    public function approve(int $id)
    {
        $user = User::where('ma_nguoi_dung', $id)->first();

        if (!$user) {
            return back()->with('error', 'Không tìm thấy user!');
        }

        $user->update([
            'trang_thai' => 1
        ]);

        // gửi mail
        try {
            Mail::raw(
                "THÔNG BÁO TỪ HỆ THỐNG QUẢN LÝ GIÁ\n\n".
                "Chào {$user->ho_ten}, tài khoản của bạn đã được duyệt!",
                function ($msg) use ($user) {
                    $msg->to($user->email)
                        ->subject('Tài khoản đã được duyệt. Bạn có thể đăng nhập ngay!');
                }
            );
        } catch (\Exception $e) {}

        return back()->with('success', '✔ Đã duyệt tài khoản!');
    }
}