@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">📦 QUẢN LÝ SẢN PHẨM</h3>
        <a href="/products/create" class="btn btn-warning fw-bold shadow-sm">+ Thêm Sản Phẩm</a>
    </div>

    {{-- Thông báo tự ẩn sau 3 giây --}}
    @if(session('success'))
        <div class="alert alert-success alertBox shadow-sm text-dark">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alertBox shadow-sm text-dark">
            {{ session('error') }}
        </div>
    @endif

    {{-- 🔍 KHUNG TÌM KIẾM (Đồng bộ kiểu Nhà Cung Cấp) --}}
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="{{ url('/products') }}" class="input-group" style="max-width: 300px;">
            <input type="text" name="keyword" class="form-control form-control-sm border-secondary" 
                   placeholder="Tìm tên sản phẩm..." 
                   value="{{ request('keyword') }}">
            
            <button class="btn btn-outline-dark btn-sm" type="submit">🔍 Tìm</button>
            
            @if(request('keyword'))
                <a href="{{ url('/products') }}" class="btn btn-outline-danger btn-sm">✕</a>
            @endif
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 text-dark fw-bold" style="width: 70px;">ID</th>
                        <th class="py-3 text-start text-dark fw-bold">Tên Sản Phẩm</th>
                        <th class="py-3 text-dark fw-bold">Danh Mục</th>
                        <th class="py-3 text-dark fw-bold" style="width: 120px;">Đơn Vị</th>
                        <th class="py-3 text-dark fw-bold">Xuất Xứ</th>
                        <th class="py-3 text-dark fw-bold">Hành Động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td class="text-dark">{{ $p->ma_san_pham }}</td>
                        <td class="text-start fw-bold text-dark">{{ $p->ten_san_pham }}</td>
                        <td class="text-dark">{{ $p->ten_danh_muc }}</td>
                        <td class="text-dark">{{ $p->don_vi_tinh }}</td>
                        <td class="text-dark small">{{ $p->xuat_xu }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="/products/edit/{{ $p->ma_san_pham }}" class="btn btn-outline-warning btn-sm mx-1">✏️ Sửa</a>
                                
                                <button onclick="confirmDelete('/products/delete/{{ $p->ma_san_pham }}')" class="btn btn-outline-danger btn-sm mx-1">
                                    🗑 Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-muted">Không tìm thấy sản phẩm nào phù hợp.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Ẩn thông báo sau 3 giây
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alertBox');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500); 
        });
    }, 3000);

    // Xác nhận xóa
    function confirmDelete(url) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
            window.location.href = url;
        }
    }
</script>

@endsection