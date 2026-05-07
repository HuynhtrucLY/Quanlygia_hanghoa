<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow-lg border-0 p-4" style="width: 450px; border-radius: 15px;">

    {{-- HEADER --}}
    <div class="text-center mb-3">
        <div class="bg-dark text-warning d-inline-flex align-items-center justify-content-center rounded-3 mb-2"
             style="width: 50px; height: 50px;">
            <i class="fas fa-chart-pie fs-4"></i>
        </div>
        <h5 class="fw-bold text-uppercase">Đăng ký tài khoản</h5>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success py-2 text-center small">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR CUSTOM --}}
    @if(session('error'))
        <div class="alert alert-danger py-2 text-center small">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="/register">
        @csrf

        <div class="mb-2">
            <label class="fw-semibold small">Họ tên</label>
            <input name="ho_ten" class="form-control" required>
        </div>

        <div class="mb-2">
            <label class="fw-semibold small">Tên đăng nhập</label>
            <input name="ten_dang_nhap" class="form-control" required>
        </div>

        <div class="mb-2">
            <label class="fw-semibold small">Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>

        <div class="mb-2">
            <label class="fw-semibold small">Mật khẩu</label>
            <input name="password" type="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="fw-semibold small">Xác nhận mật khẩu</label>
            <input name="password_confirmation" type="password" class="form-control" required>
        </div>

        <button class="btn btn-dark w-100 fw-bold">
            ĐĂNG KÝ
        </button>
    </form>

    <div class="text-center mt-3 small">
        Đã có tài khoản?
        <a href="/login" class="fw-bold text-decoration-none">Đăng nhập</a>
    </div>

</div>

</body>
</html>