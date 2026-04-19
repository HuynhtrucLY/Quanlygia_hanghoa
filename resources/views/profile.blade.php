@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%; border-radius:15px; overflow: hidden;">
        
        <div class="bg-dark text-warning p-3 text-center">
            <h4 class="m-0 text-uppercase fs-5 fw-bold">🔐 Đổi mật khẩu</h4>
        </div>

        <div class="card-body p-4">
            {{-- Thông báo thành công --}}
            @if(session('success'))
                <div class="alert alert-success text-dark shadow-sm border-0 mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Thông báo lỗi hệ thống --}}
            @if(session('error'))
                <div class="alert alert-danger text-dark shadow-sm border-0 mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Thông báo lỗi Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger text-dark shadow-sm border-0 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/change-password">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" placeholder="nhập mật khẩu hiện tại..." 
                           class="form-control text-dark border-secondary-subtle" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Mật khẩu mới</label>
                    <input type="password" name="password" placeholder="nhập mật khẩu mới..." 
                           class="form-control text-dark border-secondary-subtle" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" placeholder="nhập lại mật khẩu mới..." 
                           class="form-control text-dark border-secondary-subtle" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark shadow-sm py-2">
                    XÁC NHẬN ĐỔI MẬT KHẨU
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Chỉnh lại độ tập trung khi nhấn vào input */
    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
</style>
@endsection