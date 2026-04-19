<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --dark-grey: #2c3e50;
            --main-grey: #34495e;
            --accent-yellow: #f1c40f;
            --bg-light: #f4f7f6;
        }

        body {
            /* Thay đổi màu nền sang tông xám chuyên nghiệp của Dashboard */
            background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        .login-box {
            width: 400px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        /* Logo giống Sidebar Dashboard */
        .brand-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .logo-box {
            background: var(--accent-yellow);
            color: var(--dark-grey);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .title {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
            color: var(--dark-grey);
            margin: 0;
        }

        label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--main-grey);
            box-shadow: 0 0 0 0.25rem rgba(52, 73, 94, 0.1);
        }

        /* Nút đăng nhập màu xám đậm của Dashboard */
        .btn-login {
            background: var(--dark-grey);
            border: none;
            color: white;
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background: var(--main-grey);
            transform: translateY(-2px);
        }

        .register-link {
            color: var(--dark-grey);
            font-weight: 700;
            text-decoration: none;
        }

        .register-link:hover {
            color: #e67e22;
        }
    </style>
</head>

<body>

<div class="login-box">
    <div class="brand-logo">
        <div class="logo-box"><i class="fas fa-chart-pie"></i></div>
        <h3 class="title">Hệ thống quản lý giá</h3>
    </div>

    @if(session('success'))
        <div id="successAlert" class="alert alert-success small py-2 text-center">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <div class="mb-3">
            <label><i class="fas fa-user me-2"></i>Tài khoản</label>
            <input name="ten_dang_nhap" class="form-control" placeholder="Tên đăng nhập" required>
        </div>

        <div class="mb-4">
            <label><i class="fas fa-lock me-2"></i>Mật khẩu</label>
            <input type="password" name="mat_khau" class="form-control" placeholder="••••••••" required>
        </div>

        <button class="btn btn-login w-100 shadow-sm">Đăng nhập</button>

        <div class="text-center mt-4">
            <span class="text-muted small">Chưa có tài khoản?</span>
            <a href="/register" class="register-link small">
                Đăng ký ngay
            </a>
        </div>
    </form>

    @if(session('error'))
        <div class="alert alert-danger mt-3 small py-2 text-center">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
        </div>
    @endif
</div>

<script>
    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        let alert = document.getElementById('successAlert');
        if(alert){
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);
</script>

</body>
</html>