@extends('layouts.app')

@section('content')

{{-- Giữ nguyên Style cũ của bạn nhưng cập nhật màu sắc bên trong --}}
<style>
.card-box {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    color: #000; /* Chữ đen cho label */
}

.form-control {
    border-radius: 8px;
    height: 38px;
    color: #000; /* Chữ đen cho input */
}

/* Select2 fix - Cập nhật màu */
.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px;
    border-radius: 8px !important;
}

.select2-selection__rendered {
    line-height: 24px !important;
    color: #000 !important; /* Chữ đen trong select2 */
}

.btn-primary {
    background-color: #ffc107; /* Vàng */
    border-color: #ffc107;
    color: #000; /* Chữ đen */
    font-weight: bold;
}

.btn-primary:hover {
    background-color: #e0a800;
    border-color: #e0a800;
    color: #000;
}

.table thead {
    background: #212529; /* Đen */
    color: #ffc107; /* Vàng */
}
</style>

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm">
        <h3 class="m-0 text-uppercase fs-5">📊 SO SÁNH GIÁ CHI TIẾT</h3>
    </div>

    <div class="card-box border-0 shadow-sm">
        <form method="GET">
            <div class="row g-3">
                {{-- Kiểu --}}
                <div class="col-md-3">
                    <label class="text-dark">Kiểu so sánh</label>
                    <select name="type" id="typeSelect" class="form-control text-dark">
                        <option value="">-- Tất cả --</option>
                        <option value="supplier" {{ request('type')=='supplier'?'selected':'' }}>Theo nhà cung cấp</option>
                        <option value="month" {{ request('type')=='month'?'selected':'' }}>Theo tháng</option>
                        <option value="range" {{ request('type')=='range'?'selected':'' }}>Theo ngày</option>
                        <option value="quarter" {{ request('type')=='quarter'?'selected':'' }}>Theo quý</option>
                    </select>
                </div>

                {{-- Sản phẩm --}}
                <div class="col-md-3">
                    <label class="text-dark">Sản phẩm</label>
                    <select name="ma_san_pham" id="productFilter" class="form-control text-dark">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach($products as $pr)
                            <option value="{{ $pr->ma_san_pham }}" 
                                {{ request('ma_san_pham') == $pr->ma_san_pham ? 'selected' : '' }}>
                                {{ $pr->ten_san_pham }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Khu vực --}}
                <div class="col-md-3">
                    <label class="text-dark">Khu vực</label>
                    <select name="khu_vuc" id="regionFilter" class="form-control text-dark">
                        <option value="">-- Tất cả khu vực --</option>
                        @foreach($khuVucList as $kv)
                            <option value="{{ $kv }}" 
                                {{ request('khu_vuc') == $kv ? 'selected' : '' }}>
                                {{ $kv }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nút Lọc --}}
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100 shadow-sm text-dark">🔍 Lọc dữ liệu</button>
                </div>
            </div>

            {{-- FILTER động --}}
            <div class="row mt-3 g-3">
                <div class="col-md-4" id="monthBox">
                    <label class="text-dark">Tháng</label>
                    <input type="month" name="month" class="form-control text-dark" value="{{ request('month') }}">
                </div>
                <div class="col-md-4" id="fromBox">
                    <label class="text-dark">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control text-dark" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4" id="toBox">
                    <label class="text-dark">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control text-dark" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4" id="quarterBox">
                    <label class="text-dark">Quý</label>
                    <select name="quarter" class="form-control text-dark">
                        <option value="1">Quý I</option>
                        <option value="2">Quý II</option>
                        <option value="3">Quý III</option>
                        <option value="4">Quý IV</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Bảng So Sánh Chính --}}
    <div class="card-box border-0 shadow-sm overflow-hidden p-0">
        <table class="table table-bordered table-hover text-center mb-0 align-middle">
            <thead class="table-dark text-warning">
                <tr>
                    <th class="py-3">Sản phẩm</th>
                    <th class="py-3">Nhà cung cấp</th>
                    <th class="py-3">Giá nhập</th>
                    <th class="py-3">Giá bán</th>
                    <th class="py-3">Thị trường</th>
                    <th class="py-3">Lợi nhuận</th>
                    <th class="py-3">Chênh lệch % (Nhập/TT)</th>
                    <th class="py-3">Chênh lệch % (Bán/TT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestPrices as $p)
                @php
                    $profit = $p->gia_ban - $p->gia_nhap;
                    // 1. Tính % lệch nhập so với thị trường (Dương là đắt hơn, Âm là rẻ hơn)
                    $percent = $p->gia_thi_truong > 0 ? (($p->gia_nhap - $p->gia_thi_truong) / $p->gia_thi_truong) * 100 : 0;

                    // 2. Tính % lệch bán so với thị trường (Dương là cao hơn, Âm là thấp hơn)
                    $percentSell = $p->gia_thi_truong > 0 ? (($p->gia_ban - $p->gia_thi_truong) / $p->gia_thi_truong) * 100 : 0;
                @endphp
                <tr>
                    <td class="text-dark fw-bold">{{ $p->ten_san_pham }}</td>
                    <td class="text-start">
                        <div class="text-dark">{{ $p->ten_nha_cung_cap }}</div>
                        <small class="text-muted">{{ $p->dia_chi }}</small>
                    </td>
                    <td class="text-danger fw-bold">{{ number_format($p->gia_nhap) }} đ</td>
                    <td class="text-primary fw-bold">{{ number_format($p->gia_ban) }} đ</td>
                    <td class="text-dark">{{ number_format($p->gia_thi_truong) }} đ</td>
                    <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                        {{ number_format($profit) }} đ
                    </td>
                    <td class="{{ $percent >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($percent, 2) }}%
                    </td>
                    <td class="{{ $percentSell >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($percentSell, 2) }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Gợi ý nhà cung cấp tối ưu --}}
    @if(isset($recommend) && count($recommend) > 0)
    <div class="card-box border-0 shadow-sm mt-4 p-0 overflow-hidden">
        <div class="p-3 bg-success text-white fw-bold">
            <i class="fas fa-crown me-2"></i>🏆 NHÀ CUNG CẤP TỐI ƯU
        </div>
        <table class="table table-bordered text-center mb-0 align-middle">
            <thead class="bg-light text-dark">
                <tr>
                    <th class="py-2">Sản phẩm</th>
                    <th class="py-2">Nhà cung cấp tốt nhất</th>
                    <th class="py-2">Giá nhập</th>
                    <th class="py-2">Giá bán</th>
                    <th class="py-2">Lợi nhuận</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recommend as $r)
                <tr>
                    <td class="text-dark fw-bold">{{ $r->ten_san_pham }}</td>
                    <td>
                        <span class="badge bg-success text-white px-3 py-2">
                            {{ $r->ten_nha_cung_cap }}
                        </span>
                    </td>
                    <td class="text-danger fw-bold">{{ number_format($r->gia_nhap) }} đ</td>
                    <td class="text-primary fw-bold">{{ number_format($r->gia_ban) }} đ</td>
                    <td class="text-success fw-bold">{{ number_format($r->profit) }} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#productFilter, #regionFilter, #typeSelect').select2({ width: '100%' });

    function toggleFilter() {
        let type = $('#typeSelect').val();
        $('#monthBox, #fromBox, #toBox, #quarterBox').hide();
        if (type === 'month') $('#monthBox').show();
        if (type === 'range') $('#fromBox, #toBox').show();
        if (type === 'quarter') $('#quarterBox').show();
    }

    toggleFilter();
    $('#typeSelect').on('change', toggleFilter);
});
</script>

@endsection