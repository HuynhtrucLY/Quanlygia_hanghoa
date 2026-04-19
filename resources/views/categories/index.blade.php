@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    {{-- Header giống Nhà cung cấp --}}
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">📂 QUẢN LÝ DANH MỤC</h3>
        <a href="/categories/create" class="btn btn-warning fw-bold shadow-sm">+ Thêm Danh Mục</a>
    </div>

    {{-- Thông báo --}}
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

    {{-- 🔍 KHUNG TÌM KIẾM (Giống hệt kiểu bạn gửi) --}}
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="{{ url('/categories') }}" class="input-group" style="max-width: 300px;">
            <input type="text" name="keyword" class="form-control form-control-sm border-secondary" 
                   placeholder="Tìm tên danh mục..." 
                   value="{{ request('keyword') }}">
            
            <button class="btn btn-outline-dark btn-sm" type="submit">🔍 Tìm</button>
            
            @if(request('keyword'))
                <a href="{{ url('/categories') }}" class="btn btn-outline-danger btn-sm">✕</a>
            @endif
        </form>
    </div>

    {{-- Bảng danh mục --}}
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 text-dark fw-bold" style="width: 100px;">ID</th>
                        <th class="py-3 text-start text-dark fw-bold">Tên Danh Mục</th>
                        <th class="py-3 text-dark fw-bold" style="width: 200px;">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $cate)
                    <tr>
                        <td class="text-dark">{{ $cate->ma_danh_muc }}</td>
                        <td class="text-start fw-bold text-dark">{{ $cate->ten_danh_muc }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="/categories/edit/{{ $cate->ma_danh_muc }}" class="btn btn-outline-warning btn-sm mx-1">✏️ Sửa</a>
                                
                                <button onclick="confirmDelete('/categories/delete/{{ $cate->ma_danh_muc }}')" 
                                        class="btn btn-outline-danger btn-sm mx-1">
                                    🗑 Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-muted">Không tìm thấy danh mục nào.</td>
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
        alerts.forEach(function(alert){
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);

    // Xác nhận xóa
    function confirmDelete(url) {
        if (confirm("Bạn có chắc muốn xóa danh mục này không?")) {
            window.location.href = url;
        }
    }
</script>

@endsection