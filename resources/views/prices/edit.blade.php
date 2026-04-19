@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">📊 CẬP NHẬT BÁO GIÁ</h3>
    </div>

    {{-- 🔥 HIỂN THỊ LỖI --}}
    @if ($errors->any())
        <div class="alert alert-danger alertBox shadow-sm text-dark">
            @foreach ($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="/prices/update/{{ $price->id }}">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- 🔹 SẢN PHẨM --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Sản phẩm</label>
                        <select name="ma_san_pham" class="form-control" id="productSelect" required>
                            @foreach($products as $p)
                                <option value="{{ $p->ma_san_pham }}"
                                    {{ $price->ma_san_pham == $p->ma_san_pham ? 'selected' : '' }}>
                                    {{ $p->ten_san_pham }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔹 NHÀ CUNG CẤP --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Nhà cung cấp</label>
                        <select name="ma_nha_cung_cap" class="form-control" id="supplierSelect" required>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->ma_nha_cung_cap }}"
                                    {{ $price->ma_nha_cung_cap == $s->ma_nha_cung_cap ? 'selected' : '' }}>
                                    {{ $s->ten_nha_cung_cap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔹 GIÁ NHẬP --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-danger">Giá nhập</label>
                        <input id="gia_nhap" type="number" class="form-control"
                               name="gia_nhap" value="{{ $price->gia_nhap }}" required>
                    </div>

                    {{-- 🔹 % LỢI NHUẬN --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-dark">% Lợi nhuận</label>
                        <input id="loi_nhuan" type="text" class="form-control" name="loi_nhuan" placeholder="Nhập % lợi nhuận" required"
                               name="loi_nhuan"
                               value="{{ $price->loi_nhuan }}" required>
                    </div>

                    {{-- 🔹 GIÁ BÁN --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-primary">Giá bán</label>
                        <input id="gia_ban" class="form-control bg-light fw-bold text-primary" readonly>
                    </div>

                    {{-- 🔹 GIÁ THỊ TRƯỜNG --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-dark">Giá thị trường</label>
                        <input type="number" class="form-control"
                               name="gia_thi_truong"
                               value="{{ $price->gia_thi_truong }}" required>
                    </div>

                    {{-- 🕒 NGÀY --}}
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold text-dark">Cập nhật</label>
                        <input class="form-control bg-light"
                               value="{{ date('d/m/Y H:i') }}" disabled>
                    </div>

                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <button class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="fas fa-sync-alt"></i> Cập nhật
                    </button>

                    <a href="/prices" class="btn btn-outline-secondary px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    function tinhGia() {
        let giaNhap = parseFloat($('#gia_nhap').val()) || 0;

        let loiNhuanStr = $('#loi_nhuan').val().toString().replace('%','');
        let loiNhuan = parseFloat(loiNhuanStr) || 0;

        let giaBan = giaNhap + (giaNhap * loiNhuan / 100);

        $('#gia_ban').val(
            new Intl.NumberFormat('vi-VN').format(Math.round(giaBan)) + ' đ'
        );
    }

    // chạy lần đầu
    tinhGia();

    // nhập thay đổi
    $('#gia_nhap, #loi_nhuan').on('input', tinhGia);

    // tự thêm %
    $('#loi_nhuan').on('blur', function() {
        let val = $(this).val();
        if (val && !val.includes('%')) {
            $(this).val(val + '%');
        }
    });

});
</script>

@endsection