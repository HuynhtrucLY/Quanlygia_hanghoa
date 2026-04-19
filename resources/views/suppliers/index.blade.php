@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm d-flex justify-content-between align-items-center">
        <h3 class="m-0 text-uppercase fs-5">👤 QUẢN LÝ NHÀ CUNG CẤP</h3>
        <a href="/suppliers/create" class="btn btn-warning fw-bold shadow-sm">+ Thêm Nhà Cung Cấp</a>
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

    {{-- 🔍 KHUNG TÌM KIẾM --}}
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="{{ url('/suppliers') }}" class="input-group" style="max-width: 300px;">
            <input type="text" name="keyword" class="form-control form-control-sm border-secondary" 
                   placeholder="Tìm tên hoặc SĐT..." 
                   value="{{ request('keyword') }}">
            <button class="btn btn-outline-dark btn-sm" type="submit">🔍 Tìm</button>
            @if(request('keyword'))
                <a href="{{ url('/suppliers') }}" class="btn btn-outline-danger btn-sm">✕</a>
            @endif
        </form>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover align-middle text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 text-dark fw-bold" style="width: 80px;">ID</th>
                        <th class="py-3 text-start text-dark fw-bold">Tên Nhà Cung Cấp</th>
                        <th class="py-3 text-start text-dark fw-bold">Địa chỉ</th>
                        <th class="py-3 text-dark fw-bold">SĐT</th>
                        <th class="py-3 text-dark fw-bold">Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td class="text-dark">{{ $s->ma_nha_cung_cap }}</td>
                        <td class="text-start fw-bold text-dark">{{ $s->ten_nha_cung_cap }}</td>
                        <td class="text-start text-dark small">{{ $s->dia_chi }}</td>
                        <td class="text-dark">{{ $s->so_dien_thoai }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="/suppliers/edit/{{ $s->ma_nha_cung_cap }}" class="btn btn-outline-warning btn-sm mx-1">✏️ Sửa</a>
                                
                                <button onclick="confirmDelete('/suppliers/delete/{{ $s->ma_nha_cung_cap }}')" class="btn btn-outline-danger btn-sm mx-1">
                                    🗑 Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-muted">Không tìm thấy nhà cung cấp nào.</td>
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
        if (confirm("Bạn có chắc muốn xóa nhà cung cấp này không?")) {
            window.location.href = url;
        }
    }
</script>

@endsection