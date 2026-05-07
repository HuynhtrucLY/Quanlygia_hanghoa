@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0" style="max-width: 450px; width: 100%; border-radius:15px; overflow: hidden;">
        
        <div class="bg-dark text-warning p-3 text-center">
            <h4 class="m-0 text-uppercase fs-5 fw-bold">🔐 Đổi mật khẩu</h4>
        </div>

        <div class="card-body p-4">

            {{-- SUCCESS --}}
            @if(session('success'))
                <div class="alert alert-success text-dark shadow-sm border-0 mb-3">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- ERROR SESSION --}}
            @if(session('error'))
                <div class="alert alert-danger text-dark shadow-sm border-0 mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger text-dark shadow-sm border-0 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/change-password">
                @csrf

                {{-- MẬT KHẨU HIỆN TẠI --}}
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password"
                           class="form-control border-secondary-subtle"
                           placeholder="Nhập mật khẩu hiện tại..." required>

                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- MẬT KHẨU MỚI --}}
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Mật khẩu mới</label>
                    <input type="password" name="password"
                           class="form-control border-secondary-subtle"
                           placeholder="Nhập mật khẩu mới..." required>

                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- XÁC NHẬN MẬT KHẨU --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation"
                           class="form-control border-secondary-subtle"
                           placeholder="Nhập lại mật khẩu mới..." required>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold text-dark shadow-sm py-2">
                    XÁC NHẬN ĐỔI MẬT KHẨU
                </button>
            </form>

        </div>
    </div>
</div>
@endsection