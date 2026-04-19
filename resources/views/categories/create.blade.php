@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <div class="p-3 mb-4 bg-dark text-warning rounded shadow-sm">
        <h3 class="m-0 text-uppercase fs-5">📂 THÊM DANH MỤC MỚI</h3>
    </div>

    {{-- Lỗi validate tự ẩn sau 3 giây --}}
    @if($errors->any())
        <div class="alert alert-danger alertBox shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ url('/categories/store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Tên danh mục</label>
                    <input type="text" name="ten_danh_muc" class="form-control" placeholder="Nhập tên danh mục..." required>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning fw-bold px-4 shadow-sm text-dark">
                        <i class="fas fa-plus-circle"></i> Thêm mới
                    </button>
                    <a href="{{ url('/categories') }}" class="btn btn-outline-secondary px-4">
                        Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tự động ẩn lỗi validate sau 3 giây cho đồng bộ
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