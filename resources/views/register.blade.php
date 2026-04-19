<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --dark-sidebar: #212529; /* Màu đen xám đồng bộ */
            --accent-yellow: #ffc107; /* Màu vàng logo */
            --bg-gradient-start: #bdc3c7;
            --bg-gradient-end: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px 0;
        }

        .register-box {
            width: 450px;
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
            text-align: center;
        }

        .logo-container {
            background: var(--accent-yellow);
            color: var(--dark-sidebar);
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .system-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--dark-sidebar);
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.4;
        }

        label {
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }

        .form-control:focus {
            border-color: var(--dark-sidebar);
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.1);
        }

        .btn-register {
            background: var(--dark-sidebar);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #000000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .login-text {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
            text-align: center;
        }

        .login-link {
            color: var(--dark-sidebar);
            font-weight: 700;
            text-decoration: none;
            border-bottom: 2px solid var(--accent-yellow);
        }

        .alert ul {
            padding-left: 1rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="register-box">
    <div class="brand-header">
        <div class="logo-container">
            <i class="fas fa-chart-pie"></i>
        </div>
        <h3 class="system-title">Đăng ký tài khoản<br>Hệ thống quản lý giá</h3>
    </div>

    {{-- Lỗi Validate --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Thành công --}}
    @if(session('success'))
        <div class="alert alert-success py-2 text-center">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Lỗi Custom --}}
    @if(session('error'))
        <div class="alert alert-danger py-2 text-center">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="/register">
        @csrf

        <label><i class="fas fa-id-card me-1"></i> Họ và tên</label>
        <input name="ho_ten" class="form-control" placeholder="Nhập họ tên đầy đủ" required>

        <label><i class="fas fa-user-circle me-1"></i> Tên đăng nhập</label>
        <input name="ten_dang_nhap" class="form-control" placeholder="Tạo tài khoản" required>

        <label><i class="fas fa-envelope me-1"></i> Email</label>
        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>

        <label><i class="fas fa-key me-1"></i> Mật khẩu</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>

        <label><i class="fas fa-shield-alt me-1"></i> Xác nhận mật khẩu</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>

        <button type="submit" class="btn btn-register w-100 shadow-sm">Tạo tài khoản</button>
    </form>

    <div class="login-text">
        Đã có tài khoản? 
        <a href="/login" class="login-link">Đăng nhập ngay</a>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll("input");

    inputs.forEach(input => {
        input.addEventListener("input", function () {
            const alertBox = document.querySelector(".alert-danger");
            if (alertBox) {
                alertBox.style.display = "none";
            }
        });
    });
});
</script>
</body>
</html>