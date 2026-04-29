@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">📊 CẬP NHẬT BÁO GIÁ</h3>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            {{-- 1. HIỂN THỊ THÔNG BÁO LỖI/THÀNH CÔNG TỪ SERVER --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="/prices/store" id="priceForm">
                @csrf

                <div class="row">
                    {{-- 🔹 SẢN PHẨM --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Sản phẩm</label>
                        <select name="ma_san_pham" class="form-control" id="productSelect" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->ma_san_pham }}">{{ $p->ten_san_pham }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔹 NHÀ CUNG CẤP --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Nhà cung cấp</label>
                        <select name="ma_nha_cung_cap" class="form-control" id="supplierSelect" required>
                            <option value="">-- Chọn nhà cung cấp --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->ma_nha_cung_cap }}">{{ $s->ten_nha_cung_cap }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔥 GIÁ NHẬP --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-danger">Giá nhập</label>
                        <input id="gia_nhap" type="number" class="form-control" name="gia_nhap" placeholder="0" min="1" required>
                    </div>

                    {{-- 🔥 % LỢI NHUẬN --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-dark">% Lợi nhuận mục tiêu</label>
                        <input id="loi_nhuan" type="text" class="form-control" name="loi_nhuan" placeholder="Nhập % lợi nhuận" required>
                    </div>

                    {{-- 🔥 GIÁ BÁN DỰ KIẾN --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-primary">Giá bán dự kiến</label>
                        <input id="gia_ban" class="form-control bg-light fw-bold text-primary" readonly placeholder="Tự động tính...">
                        {{-- KHU VỰC HIỂN THỊ CẢNH BÁO THÔNG MINH --}}
                        <div id="alert-message" class="mt-2 fw-bold text-danger" style="font-size: 0.85rem; min-height: 20px;"></div>
                    </div>

                    {{-- 🔥 GIÁ THỊ TRƯỜNG --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Giá thị trường (Để so sánh)</label>
                        <input id="gia_thi_truong" type="number" class="form-control" name="gia_thi_truong" placeholder="0" min="1" required>
                    </div>

                    {{-- 🕒 NGÀY --}}
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Ngày cập nhật</label>
                        <input class="form-control bg-light" value="{{ date('d/m/Y H:i') }}" disabled>
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="fas fa-save"></i> Lưu báo giá
                    </button>
                    <a href="{{ url('/prices') }}" class="btn btn-outline-secondary px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // Khởi tạo Select2 cho tìm kiếm nhanh
    $('#productSelect, #supplierSelect').select2({ width: '100%' });

    // 🔥 TỰ ĐỘNG THÊM DẤU % KHI RỜI Ô NHẬP
    $('#loi_nhuan').on('blur', function() {
        let val = $(this).val().replace('%','').trim();
        if(val !== '' && !isNaN(val)) {
            $(this).val(val + '%');
        }
    });

    // 🔥 LOGIC TÍNH TOÁN VÀ CẢNH BÁO THỜI GIAN THỰC
    $('#gia_nhap, #loi_nhuan, #gia_thi_truong').on('input change', function() {
        
        // Lấy dữ liệu
        let giaNhap = parseFloat($('#gia_nhap').val()) || 0;
        let giaThiTruong = parseFloat($('#gia_thi_truong').val()) || 0;
        let loiNhuanRaw = $('#loi_nhuan').val().replace('%','');
        let loiNhuan = parseFloat(loiNhuanRaw) || 0;

        // 1. Tính giá bán dự kiến
        let giaBan = giaNhap + (giaNhap * loiNhuan / 100);
        let rounded = Math.round(giaBan);

        // Hiển thị giá bán (định dạng tiền tệ VN)
        if (giaNhap > 0) {
            $('#gia_ban').val(new Intl.NumberFormat('vi-VN').format(rounded) + ' đ');
        } else {
            $('#gia_ban').val('');
        }

        // 2. KIỂM TRA CÁC ĐIỀU KIỆN CẢNH BÁO
        let alertBox = $('#alert-message');
        let messages = [];

        if (giaNhap > 0) {
            // Cảnh báo 1: Lợi nhuận quá thấp
            if (loiNhuan < 5) {
                messages.push('<i class="fas fa-exclamation-circle"></i> Cảnh báo: Lợi nhuận mục tiêu đang dưới 5%!');
            }

            if (giaThiTruong > 0) {
                // Cảnh báo 2: Giá nhập cao hơn thị trường
                if (giaNhap > giaThiTruong) {
                    messages.push('<i class="fas fa-exclamation-triangle"></i> Cảnh báo: Giá nhập đang cao hơn giá thị trường!');
                }

                // Cảnh báo 3: Giá bán cao hơn thị trường
                if (giaBan > giaThiTruong) {
                    messages.push('<i class="fas fa-info-circle"></i> Lưu ý: Giá bán dự kiến đang cao hơn thị trường!');
                }
            }
        }

        // Xuất cảnh báo ra màn hình
        alertBox.html(messages.join('<br>'));
    });

    // CHẶN GỬI FORM NẾU GIÁ BẰNG 0 (Bảo mật thêm ở Frontend)
    $('#priceForm').on('submit', function() {
        let giaNhap = parseFloat($('#gia_nhap').val()) || 0;
        if (giaNhap <= 0) {
            alert("Vui lòng nhập giá nhập hợp lệ (lớn hơn 0)!");
            return false;
        }
    });

});
</script>

@endsection