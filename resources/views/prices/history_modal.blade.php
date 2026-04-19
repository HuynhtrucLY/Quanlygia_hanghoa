@extends('layouts.app')

@section('title', 'Báo cáo lịch sử giá')

@section('content')
<div class="container py-4">
    <div class="card p-4" style="box-shadow: 0 0 25px rgba(0,0,0,0.08); border-radius:12px;">
        <!-- Nút quay lại nhỏ gọn -->
        <div class="d-flex justify-content-start">
            <a href="/prices" class="btn btn-light btn-sm mb-3 text-muted" 
               style="padding: 2px 8px; font-size: 1rem; border: 1px solid #eee; background: #fafafa;">
                <i class="fas fa-chevron-left" style="font-size:1rem;"></i> Quay lại
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-uppercase m-0">📊 Báo cáo biến động giá</h3>
            <div class="text-end text-muted small">
                Ngày xuất: {{ date('d/m/Y H:i') }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Nhà cung cấp</th>
                        <th>Thời gian</th>
                        <th>Giá nhập cũ</th>
                        <th>Giá nhập mới</th>
                        <th>Giá thị trường</th>
                        <th>Mức biến động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                        @php
                            $diff = $h->gia_nhap_moi - $h->gia_nhap_cu;
                            $percent = ($h->gia_nhap_cu > 0) ? ($diff / $h->gia_nhap_cu) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $h->ten_san_pham }}</td>
                            <td>{{ $h->ten_nha_cung_cap }}</td>
                            <td class="text-muted">{{ date('d/m/Y H:i', strtotime($h->thoi_gian_thay_doi)) }}</td>
                            <td>{{ number_format($h->gia_nhap_cu, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($h->gia_nhap_moi, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($h->gia_thi_truong_luc_do, 0, ',', '.') }} đ</td>
                            <td class="text-center">
                                @if($diff > 0)
                                    <span class="badge bg-danger">
                                        ▲ +{{ number_format($diff, 0, ',', '.') }} đ
                                    </span>
                                @elseif($diff < 0)
                                    <span class="badge bg-success">
                                        ▼ {{ number_format($diff, 0, ',', '.') }} đ
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Không đổi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">Không có dữ liệu lịch sử.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-center">
            <div class="mt-4 text-center">
    <a href="{{ url('/export-history?ma_san_pham=' . $history[0]->ma_san_pham . '&ma_nha_cung_cap=' . $history[0]->ma_nha_cung_cap) }}" 
       class="btn btn-success btn-sm">
        📥 Xuất Excel
    </a>
</div>
        </div>
    </div>
</div>

<style>
    /* Custom styles trong layout */
    table tbody tr:hover { background: #f3f8ff; transition: 0.2s; }
    .badge-delta { min-width: 110px; padding: 8px; font-size: 0.9rem; border-radius: 10px; }
    @media print {
        .btn, a.btn { display: none; }
    }
</style>
@endsection