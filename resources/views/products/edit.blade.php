@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm">
        <h3 class="m-0 text-uppercase fs-5">📦 CHỈNH SỬA SẢN PHẨM</h3>
    </div>

    {{-- Lỗi validate (nếu có) --}}
    @if($errors->any())
        <div class="alert alert-danger alertBox shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="/products/update/{{ $product->ma_san_pham }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Tên sản phẩm</label>
                        <input class="form-control" name="ten_san_pham" value="{{ $product->ten_san_pham }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Danh mục</label>
                        <select name="ma_danh_muc" class="form-control" id="categorySelect">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->ma_danh_muc }}"
                                    {{ $product->ma_danh_muc == $c->ma_danh_muc ? 'selected' : '' }}>
                                    {{ $c->ten_danh_muc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Đơn vị tính</label>
                        <input class="form-control" name="don_vi_tinh" value="{{ $product->don_vi_tinh }}" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Xuất xứ</label>
                        <input class="form-control" name="xuat_xu" value="{{ $product->xuat_xu }}">
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <button class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="fas fa-save"></i> Cập nhật ngay
                    </button>
                    <a href="{{ url('/products') }}" class="btn btn-outline-secondary px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🔹 LOAD JQUERY --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- 🔹 LOAD SELECT2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Kích hoạt Select2
    $('#categorySelect').select2({
        placeholder: "-- Chọn danh mục --",
        width: '100%', // Đảm bảo rộng hết cột
        allowClear: false
    });

    // Tự động ẩn lỗi sau 3 giây
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alertBox');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500); 
        });
    }, 3000);
});
</script>

@endsection