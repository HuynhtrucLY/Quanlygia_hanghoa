@extends('layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')

<div class="container-fluid py-4">
    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div class="alert alert-success alertBox shadow-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alertBox shadow-sm">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alertBox shadow-sm">
            @foreach ($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    {{-- TIÊU ĐỀ - Dùng class chuẩn bg-dark và text-warning --}}
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm">
        <h3 class="m-0 text-uppercase fs-5">➕ QUẢN LÝ NGƯỜI DÙNG</h3>
    </div>

    {{-- FORM THÊM MỚI --}}
    <div class="card p-4 mb-4 shadow-sm border-0">
        <form method="POST" action="/users/store">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="fw-bold small text-dark">HỌ TÊN</label>
                    <input name="ho_ten" class="form-control" required placeholder="Nhập họ tên...">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold small text-dark">TÊN ĐĂNG NHẬP</label>
                    <input name="ten_dang_nhap" class="form-control" required placeholder="Tài khoản...">
                </div>

                <div class="col-md-3">
                    <label class="fw-bold small text-dark">EMAIL</label>
                    <input name="email" type="email" class="form-control" required placeholder="Nhập email...">
                </div>

                <div class="col-md-2">
                    <label class="fw-bold small text-dark">MẬT KHẨU</label>
                    <input name="password" type="password" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <label class="fw-bold small text-dark">VAI TRÒ</label>
                    <select name="vai_tro" class="form-select" required>
                        <option value="" disabled selected>-- Chọn --</option>
                        <option value="admin">Admin</option>
                        <option value="nhanvien">Nhân viên</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="fw-bold small text-dark">TRẠNG THÁI</label>
                    <select name="trang_thai" class="form-select" required>
                        <option value="" disabled selected>-- Chọn --</option>
                        <option value="1">Hoạt động</option>
                        <option value="0">Ngừng</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-warning fw-bold mt-3 px-4 shadow-sm text-dark">Thêm người dùng</button>
        </form>
    </div>

    {{-- BẢNG DANH SÁCH --}}
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-dark text-warning">
            <h5 class="m-0 fs-6 text-uppercase">📋 DANH SÁCH NGƯỜI DÙNG</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="py-3">Họ tên</th>
                        <th class="py-3">Tên đăng nhập</th>
                        <th class="py-3">Vai trò</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="fw-bold text-dark">{{ $u->ho_ten }}</td>
                        <td class="text-muted">{{ $u->ten_dang_nhap }}</td>

                        <td>
                            @if($u->vai_tro == 'admin')
                                <span class="badge bg-dark text-warning border border-warning">Admin</span>
                            @else
                                <span class="badge bg-secondary">Nhân viên</span>
                            @endif
                        </td>

                        <td>
                            @if($u->vai_tro == 'admin')
                                 <span class="badge bg-dark text-warning border border-warning">Admin</span>
                            @else
                                @if((int)$u->trang_thai === 0)
                                    <form method="POST" action="/users/approve/{{ $u->ma_nguoi_dung }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-warning btn-sm fw-bold">Duyệt</button>
                                    </form>
                                @elseif((int)$u->trang_thai === 1)
                                    <form method="POST" action="/users/toggle/{{ $u->ma_nguoi_dung }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Hoạt động</button>
                                    </form>
                                @elseif((int)$u->trang_thai === 2)
                                    <form method="POST" action="/users/toggle/{{ $u->ma_nguoi_dung }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">Bị khóa</button>
                                    </form>
                                @endif
                            @endif
                        </td>

                        <td>
                            @if($u->vai_tro != 'admin')
                                <form method="POST" action="/users/delete/{{ $u->ma_nguoi_dung }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-danger btn-sm mx-1" onclick="return confirm('Xóa user này?')">Xóa</button>
                                </form>
                                <form method="POST" action="/users/reset/{{ $u->ma_nguoi_dung }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-secondary btn-sm mx-1" onclick="return confirm('Reset mật khẩu?')">Reset</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alertBox');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500); 
        });
    }, 3000);
</script>

@endsection