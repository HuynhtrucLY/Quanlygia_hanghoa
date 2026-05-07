@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5 fw-bold">🚨 CẢNH BÁO BIẾN ĐỘNG GIÁ</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center"> 
                <thead class="bg-light">
                    <tr class="text-secondary small">
                        <th class="py-3 fw-bold">SẢN PHẨM</th>
                        <th class="py-3 fw-bold">NHÀ CUNG CẤP</th>
                        <th class="py-3 fw-bold">GIÁ NHẬP</th>
                        <th class="py-3 fw-bold">GIÁ BÁN</th>
                        <th class="py-3 fw-bold">GIÁ THỊ TRƯỜNG</th>
                        <th class="py-3 fw-bold">CẢNH BÁO</th>
                        <th class="py-3 fw-bold">LỢI NHUẬN</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($alerts as $a)
                    @php
                        $profit = $a->gia_ban - $a->gia_nhap;
                        $profitPercent = $a->gia_nhap > 0 ? ($profit / $a->gia_nhap) * 100 : 0;
                        $isSellHigh = $a->gia_ban > $a->gia_thi_truong;
                        $isBuyHigh  = $a->gia_nhap > $a->gia_thi_truong;
                    @endphp

                    <tr>
                        <td class="py-3 fw-bold text-dark text-start ps-3">{{ $a->ten_san_pham }}</td>
                        
                        <td class="text-secondary small">{{ $a->ten_nha_cung_cap }}</td>

                        <td class="fw-bold text-danger">
                            {{ number_format($a->gia_nhap, 0, ',', '.') }} đ
                        </td>

                        <td class="fw-bold text-primary">
                            {{ number_format($a->gia_ban, 0, ',', '.') }} đ
                        </td>

                        <td class="text-muted">
                            {{ number_format($a->gia_thi_truong, 0, ',', '.') }} đ
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                @if($isSellHigh)
                                    <span class="badge border border-danger text-danger fw-normal rounded-1">Bán cao</span>
                                @endif
                                @if($isBuyHigh)
                                    <span class="badge border border-warning text-warning fw-normal rounded-1">Nhập cao</span>
                                @endif
                                @if($profitPercent < 5)
                                    <span class="badge border border-secondary text-secondary fw-normal rounded-1">Lãi thấp</span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <span class="fw-bold {{ $profit < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($profit, 0, ',', '.') }} đ
                            </span>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                ({{ number_format($profitPercent, 1) }}%)
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted">
                            Hiện chưa có cảnh báo nào từ hệ thống.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection