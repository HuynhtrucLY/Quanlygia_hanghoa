<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{
    // Hiển thị danh sách
    public function index()
    {
        // Chặn không phải admin
        if (Auth::user()->vai_tro != 'admin') {
            abort(403);
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

    // 🔹 Thêm user
    public function store(Request $request)
{
    $request->validate([
        'ho_ten' => 'required',
        'ten_dang_nhap' => 'required|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ], [
        'ho_ten.required' => 'Vui lòng nhập họ tên',
        'ten_dang_nhap.required' => 'Vui lòng nhập tên đăng nhập',
        'ten_dang_nhap.unique' => 'Tên đăng nhập đã tồn tại',
        'email.required' => 'Vui lòng nhập email',
        'email.email' => 'Email không hợp lệ',
        'email.unique' => 'Email đã tồn tại',
        'password.required' => 'Vui lòng nhập mật khẩu',
        'password.min' => 'Mật khẩu phải ít nhất 6 ký tự'
    ]);

    // Tạo user
    $user = User::create([
        'ho_ten' => $request->ho_ten,
        'ten_dang_nhap' => $request->ten_dang_nhap,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'vai_tro' => $request->vai_tro,
        'trang_thai' => $request->trang_thai
    ]);

    // GỬI MAIL
    try {
        Mail::raw(
            "THÔNG BÁO TỪ HỆ THỐNG QUẢN LÝ GIÁ\n\n".
            "🎉 Tài khoản của bạn đã được tạo!\n\n".
            "👤 Họ tên: {$user->ho_ten}\n".
            "🔑 Tên đăng nhập: {$user->ten_dang_nhap}\n".
            "🔒 Mật khẩu: {$request->password}\n\n".
            "👉 Bạn có thể đăng nhập vào hệ thống với thông tin trên.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Tạo tài khoản thành công');
            }
        );
    } catch (\Exception $e) {
    }

    return back()->with('success', '✔ Thêm user + gửi mail thành công!');
}

    // Xóa user
    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'User không tồn tại!');
        }

        // Không cho xóa chính mình
        if (Auth::user()->ma_nguoi_dung == $id) {
            return back()->with('error', 'Không thể xóa chính mình!');
        }

        // Không cho xóa admin
        if ($user->vai_tro == 'admin') {
            return back()->with('error', 'Không thể xóa tài khoản admin!');
        }

        $user->delete();

        return back()->with('success', 'Đã xóa!');
    }

    public function toggle($id)
{
    $user = User::where('ma_nguoi_dung', $id)->first();

    if ($user->trang_thai == 1) {
        $newStatus = 2; // khóa
    } else {
        $newStatus = 1; // mở lại
    }

    User::where('ma_nguoi_dung', $id)->update([
        'trang_thai' => $newStatus
    ]);

    return back()->with('success', '✔ Đã cập nhật trạng thái!');
}

public function reset($ma_nguoi_dung)
{
    $user = User::where('ma_nguoi_dung', $ma_nguoi_dung)->first();

    if (!$user) {
        return back()->with('error', 'User không tồn tại!');
    }

    // reset về mật khẩu mặc định
    $user->update([
        'password' => Hash::make('123456')
    ]);

    return back()->with('success', '✔ Đã reset mật khẩu về 123456');
}
}