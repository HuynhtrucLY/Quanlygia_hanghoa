@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">📊 QUẢN LÝ BÁO GIÁ</h3>
        <div class="d-flex gap-2">
            <a href="/prices/create" class="btn btn-warning fw-bold shadow-sm">+ Thêm Báo giá</a>
        </div>
    </div>

    {{-- Khu vực Import Excel --}}
    <div class="card p-4 mb-4 shadow-sm border-0">
        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-file-excel"></i> NHẬP DỮ LIỆU TỪ EXCEL</h6>
        <form action="/import-excel" method="POST" enctype="multipart/form-data" class="row g-2">
            @csrf
            <div class="col-md-6 col-lg-4">
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-3">
                <button class="btn btn-dark fw-bold px-3">📥 Import ngay</button>
            </div>
        </form>
    </div>

    {{-- Thông báo tự ẩn --}}
    @if(session('success'))
        <div class="alert alert-success alertBox shadow-sm border-0 text-dark" style="line-height:1.8">
            {!! nl2br(session('success')) !!}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alertBox shadow-sm border-0 text-dark">{{ session('error') }}</div>
    @endif

    {{-- 🔍 KHUNG TÌM KIẾM (Đồng bộ kiểu Nhà Cung Cấp) --}}
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="{{ url('/prices') }}" class="input-group" style="max-width: 350px;">
            <input type="text" name="keyword" class="form-control form-control-sm border-secondary" 
                   placeholder="Tìm sản phẩm hoặc NCC..." 
                   value="{{ request('keyword') }}">
            
            <button class="btn btn-outline-dark btn-sm" type="submit">🔍 Tìm</button>
            
            @if(request('keyword'))
                <a href="{{ url('/prices') }}" class="btn btn-outline-danger btn-sm">✕</a>
            @endif
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="py-3 text-dark fw-bold" style="width: 60px;">ID</th>
                        <th class="py-3 text-start text-dark fw-bold">Sản phẩm</th>
                        <th class="py-3 text-start text-dark fw-bold">Nhà cung cấp</th>
                        <th class="py-3 text-dark fw-bold">Giá nhập</th>
                        <th class="py-3 text-dark fw-bold">Giá bán</th>
                        <th class="py-3 text-dark fw-bold">Giá thị trường</th>
                        <th class="py-3 text-dark fw-bold">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $p)
                    <tr>
                        <td class="text-muted small">{{ $p->id }}</td>
                        <td class="text-start fw-bold text-dark">{{ $p->ten_san_pham }}</td>
                        <td class="text-start text-dark">{{ $p->ten_nha_cung_cap }}</td>
                        <td class="fw-bold text-danger">{{ number_format($p->gia_nhap, 0, ',', '.') }} đ</td>
                        <td class="fw-bold text-primary">{{ number_format($p->gia_ban, 0, ',', '.') }} đ</td>
                        <td class="text-muted small">{{ number_format($p->gia_thi_truong, 0, ',', '.') }} đ</td>
                        <td>
                            <div class="btn-group">
                                <a href="/prices/edit/{{ $p->id }}" class="btn btn-outline-warning btn-sm mx-1">✏️ Sửa</a>

                                <a href="{{ url('/prices/history/' . $p->ma_san_pham . '/' . $p->ma_nha_cung_cap) }}" 
                                   class="btn btn-outline-dark btn-sm mx-1">
                                    🕒 Lịch sử
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 text-muted">Không tìm thấy báo giá nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Ẩn tất cả thông báo sau 3 giây
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alertBox');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500); 
        });
    }, 3000);
</script>

@endsection