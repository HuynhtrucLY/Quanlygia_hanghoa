@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex align-items-center">
        <h3 class="m-0 text-uppercase fs-5 fw-bold">📊 SO SÁNH GIÁ CHI TIẾT</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-white">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark">Kiểu so sánh</label>
                    <select name="type" id="typeSelect" class="form-select shadow-none">
                        <option value="">-- Tất cả --</option>
                        <option value="supplier" {{ request('type')=='supplier'?'selected':'' }}>Theo nhà cung cấp</option>
                        <option value="month" {{ request('type')=='month'?'selected':'' }}>Theo tháng</option>
                        <option value="range" {{ request('type')=='range'?'selected':'' }}>Theo ngày</option>
                        <option value="quarter" {{ request('type')=='quarter'?'selected':'' }}>Theo quý</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark">Sản phẩm</label>
                    <select name="ma_san_pham" id="productFilter" class="form-select shadow-none">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach($products as $pr)
                            <option value="{{ $pr->ma_san_pham }}" 
                                {{ request('ma_san_pham') == $pr->ma_san_pham ? 'selected' : '' }}>
                                {{ $pr->ten_san_pham }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark">Khu vực</label>
                    <select name="khu_vuc" id="regionFilter" class="form-select shadow-none">
                        <option value="">-- Tất cả khu vực --</option>
                        @foreach($khuVucList as $kv)
                            <option value="{{ $kv }}" 
                                {{ request('khu_vuc') == $kv ? 'selected' : '' }}>
                                {{ $kv }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-warning w-100 fw-bold shadow-sm border-0 py-2">
                        🔍 Lọc dữ liệu
                    </button>
                </div>
            </div>

            <div class="row mt-3 g-3">
                <div class="col-md-4" id="monthBox" style="display:none;">
                    <label class="form-label fw-bold text-dark">Tháng</label>
                    <input type="month" name="month" class="form-control shadow-none" value="{{ request('month') }}">
                </div>
                <div class="col-md-4" id="fromBox" style="display:none;">
                    <label class="form-label fw-bold text-dark">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control shadow-none" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4" id="toBox" style="display:none;">
                    <label class="form-label fw-bold text-dark">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control shadow-none" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4" id="quarterBox" style="display:none;">
                    <label class="form-label fw-bold text-dark">Quý</label>
                    <select name="quarter" class="form-select shadow-none">
    <option value="1" {{ request('quarter') == 1 ? 'selected' : '' }}>Quý I</option>
    <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>Quý II</option>
    <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>Quý III</option>
    <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>Quý IV</option>
</select>
                </div>
            </div>
        </form>
    </div>

     <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light text-secondary">
                    <tr class="text-warning border-bottom-0">
                        <th class="py-3 fw-bold">Sản phẩm</th>
                        <th class="py-3 fw-bold">Nhà cung cấp</th>
                        <th class="py-3 fw-bold">Giá nhập</th>
                        <th class="py-3 fw-bold">Giá bán</th>
                        <th class="py-3 fw-bold">Giá thị trường</th>
                        <th class="py-3 fw-bold">Lợi nhuận</th>
                        <th class="py-3 fw-bold text-nowrap">Chênh lệch % (N/TT)</th>
                        <th class="py-3 fw-bold text-nowrap">Chênh lệch % (B/TT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestPrices as $p)
                    @php
                        $profit = $p->gia_ban - $p->gia_nhap;
                        $percent = $p->gia_thi_truong > 0 ? (($p->gia_nhap - $p->gia_thi_truong) / $p->gia_thi_truong) * 100 : 0;
                        $percentSell = $p->gia_thi_truong > 0 ? (($p->gia_ban - $p->gia_thi_truong) / $p->gia_thi_truong) * 100 : 0;
                    @endphp
                    <tr class="border-bottom">
                        <td class="text-dark fw-bold">{{ $p->ten_san_pham }}</td>
                        <td class="text-start">
                            <div class="fw-semibold text-dark">{{ $p->ten_nha_cung_cap }}</div>
                            <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $p->dia_chi }}</small>
                        </td>
                        <td class="text-danger fw-bold text-nowrap">{{ number_format($p->gia_nhap) }} đ</td>
                        <td class="text-primary fw-bold text-nowrap">{{ number_format($p->gia_ban) }} đ</td>
                        <td class="text-secondary text-nowrap">{{ number_format($p->gia_thi_truong) }} đ</td>
                        <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold text-nowrap">
                            {{ number_format($profit) }} đ
                        </td>
                        <td class="{{ $percent >= 0 ? 'text-success' : 'text-success' }} fw-semibold">
                            {{ number_format($percent, 2) }}%
                        </td>
                        <td class="{{ $percentSell >= 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                            {{ number_format($percentSell, 2) }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($recommend) && count($recommend) > 0)
    <div class="card border-0 shadow-sm rounded-3 mt-4 overflow-hidden bg-white">
        <div class="p-3 bg-success text-white fw-bold">
            🏆 NHÀ CUNG CẤP TỐI ƯU
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="bg-light">
                    <tr class="text-dark border-bottom">
                        <th class="py-3">Sản phẩm</th>
                        <th class="py-3">Nhà cung cấp tốt nhất</th>
                        <th class="py-3">Giá nhập</th>
                        <th class="py-3">Giá bán</th>
                        <th class="py-3">Lợi nhuận</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recommend as $r)
                    <tr>
                        <td class="text-dark fw-bold">{{ $r->ten_san_pham }}</td>
                        <td>
                            <span class="badge rounded-pill bg-success px-3 py-2">
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
    </div>
    @endif
</div>

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