@extends('layouts.app')

@section('title', 'Dashboard Quản lý giá hàng hóa')

@section('content')

    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="fw-bold m-0 text-uppercase fs-5">📊 Dashboard tổng quan</h3>
    </div>

    <div class="row mb-2">
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-dark kpi-card">
                <small class="text-muted fw-bold">TỔNG SẢN PHẨM</small>
                <h3 class="text-dark">{{ $totalProducts }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-start border-4 border-warning kpi-card">
                <small class="text-muted fw-bold">NHÀ CUNG CẤP</small>
                <h3 class="text-dark">{{ $totalSuppliers }}</h3>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-4 border-0 shadow-sm">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="ma_san_pham" class="form-select text-dark border-secondary-subtle">
                    <option value="">-- Tất cả sản phẩm --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->ma_san_pham }}" {{ request('ma_san_pham') == $p->ma_san_pham ? 'selected' : '' }}>
                            {{ $p->ten_san_pham }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-warning fw-bold text-dark w-100 shadow-sm">
                    <i class="fas fa-filter me-1"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card p-4 shadow-sm border-0 mb-4">
                <h5 class="section-title text-dark fw-bold"><i class="fas fa-chart-bar me-2 text-dark"></i>So sánh giá (Nhập - Bán - Thị trường)</h5>
                <div style="height: 350px;">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 shadow-sm border-0 mb-4">
                <h5 class="section-title text-dark fw-bold"><i class="fas fa-chart-line me-2 text-success"></i>Lợi nhuận (Bán - Nhập)</h5>
                <div style="height: 300px;">
                    <canvas id="profitChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 shadow-sm border-0 mb-4">
                <h5 class="section-title text-dark fw-bold"><i class="fas fa-exchange-alt me-2 text-warning"></i>Chênh lệch giá bán với giá thị trường</h5>
                <div style="height: 300px;">
                    <canvas id="marketChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2 p-4 shadow-sm border-0">
        <h5 class="section-title text-danger fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Chi tiết cảnh báo Lời / Lỗ</h5>

        @php $grouped = []; @endphp
        @foreach($data as $item)
            @php $grouped[$item->ten_san_pham][] = $item; @endphp
        @endforeach

        <div class="row">
            @foreach($grouped as $product => $items)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">{{ $product }}</h6>
                        <ul class="list-unstyled m-0">
                            @foreach($items as $item)
                                @php $profit = $item->gia_ban - $item->gia_nhap; @endphp
                                <li class="d-flex justify-content-between mb-1">
                                    <small class="text-dark">{{ $item->ten_nha_cung_cap }}:</small>
                                    @if($profit < 0)
                                        <span class="text-danger small fw-bold">🔴 Lỗ {{ number_format($profit) }}đ</span>
                                    @else
                                        <span class="text-success small fw-bold">🟢 Lời {{ number_format($profit) }}đ</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let rawData = @json($data);

    let labels = [];
    let giaNhap = [];
    let giaBan = [];
    let giaThiTruong = [];
    let profitData = [];
    let marketDiff = [];

    rawData.forEach(item => {
        labels.push(item.ten_san_pham + " (" + item.ten_nha_cung_cap + ")");
        giaNhap.push(item.gia_nhap);
        giaBan.push(item.gia_ban);
        giaThiTruong.push(item.gia_thi_truong);
        profitData.push(item.gia_ban - item.gia_nhap);
        marketDiff.push(item.gia_thi_truong - item.gia_ban);
    });

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    };

    new Chart(document.getElementById('chart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Giá nhập', data: giaNhap, backgroundColor: '#6c757d' },
                { label: 'Giá bán', data: giaBan, backgroundColor: '#28a745' },
                { label: 'Giá thị trường', data: giaThiTruong, backgroundColor: '#ffc107' }
            ]
        },
        options: chartOptions
    });

    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{ 
                label: 'Mức lợi nhuận', 
                data: profitData, 
                borderColor: '#28a745', 
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.3 
            }]
        },
        options: chartOptions
    });

    new Chart(document.getElementById('marketChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ 
                label: 'Chênh lệch', 
                data: marketDiff,
                backgroundColor: marketDiff.map(v => v >= 0 ? '#20c997' : '#dc3545')
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return value >= 0 ? "Rẻ hơn TT: " + value.toLocaleString() + "đ" : "Mắc hơn TT: " + Math.abs(value).toLocaleString() + "đ";
                        }
                    }
                }
            }
        }
    });
</script>
@endsection