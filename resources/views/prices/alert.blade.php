@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5 fw-bold">🚨 CẢNH BÁO BIẾN ĐỘNG GIÁ</h3>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-muted fw-bold small">SẢN PHẨM</th>
                        <th class="py-3 text-muted fw-bold small">NHÀ CUNG CẤP</th>
                        <th class="py-3 text-muted fw-bold small text-end">GIÁ NHẬP</th>
                        <th class="py-3 text-muted fw-bold small text-end">GIÁ BÁN</th>
                        <th class="py-3 text-muted fw-bold small text-end">THỊ TRƯỜNG</th>
                        <th class="py-3 text-muted fw-bold small text-center">CẢNH BÁO</th>
                        <th class="pe-4 py-3 text-muted fw-bold small text-end">LỢI NHUẬN</th>
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

                    <tr class="border-bottom">
                        <td class="ps-4 py-3 fw-bold text-dark">{{ $a->ten_san_pham }}</td>
                        <td class="text-secondary small">{{ $a->ten_nha_cung_cap }}</td>

                        <td class="text-end fw-semibold {{ $isBuyHigh ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($a->gia_nhap, 0, ',', '.') }} đ
                        </td>

                        <td class="text-end fw-semibold {{ $isSellHigh ? 'text-danger' : 'text-primary' }}">
                            {{ number_format($a->gia_ban, 0, ',', '.') }} đ
                        </td>

                        <td class="text-end text-muted small">
                            {{ number_format($a->gia_thi_truong, 0, ',', '.') }} đ
                        </td>

                        <td class="text-center px-2">
                            <div class="d-flex flex-row justify-content-center gap-1 flex-wrap">
                                @if($isSellHigh)
                                    <span class="badge border border-danger text-danger fw-normal rounded-1 px-2 py-1" style="font-size: 0.7rem;">Bán cao</span>
                                @endif
                                @if($isBuyHigh)
                                    <span class="badge border border-warning text-warning fw-normal rounded-1 px-2 py-1" style="font-size: 0.7rem;">Nhập cao</span>
                                @endif
                                @if($profitPercent < 5)
                                    <span class="badge border border-secondary text-secondary fw-normal rounded-1 px-2 py-1" style="font-size: 0.7rem;">Lãi thấp</span>
                                @endif
                            </div>
                        </td>

                        <td class="pe-4 text-end">
                            <span class="fw-bold {{ $profit < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($profit, 0, ',', '.') }} đ
                            </span>
                            <div class="text-muted" style="font-size: 0.7rem;">
                                ({{ number_format($profitPercent, 1) }}%)
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted small">
                            Hiện chưa có cảnh báo nào từ hệ thống.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    tr:hover { background-color: #fafafa; transition: 0.2s; }
</style>

@endsection