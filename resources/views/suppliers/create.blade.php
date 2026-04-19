@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm">
        <h3 class="m-0 text-uppercase fs-4">👤 THÊM NHÀ CUNG CẤP MỚI</h3>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="/suppliers/store">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Tên nhà cung cấp</label>
                    <input class="form-control" name="ten_nha_cung_cap" placeholder="Nhập tên..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Địa chỉ</label>
                    <input class="form-control" name="dia_chi" placeholder="Nhập địa chỉ..." required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Số điện thoại</label>
                    <input class="form-control" name="so_dien_thoai" placeholder="Nhập SĐT..." required>
                    </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning fw-bold px-4 shadow-sm">
                        <i class="fas fa-save"></i> Lưu thông tin
                    </button>
                    <a href="{{ url('/suppliers') }}" class="btn btn-outline-secondary px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection