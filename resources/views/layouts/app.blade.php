<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý giá - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --main-grey: #343a40; 
            --dark-grey: #212529;
            --light-grey: #f4f6f9;
            --primary-color: #ffc107;
        }

        body { background: var(--light-grey); display: flex; min-height: 100vh; margin: 0; font-family: 'Inter', sans-serif; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--main-grey);
            color: white;
            position: fixed;
            height: 100%;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 20px;
            font-size: 1.1rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--dark-grey); /* Nền đen cho logo */
            border-bottom: 1px solid #4b545c;
        }

        .logo-icon {
            background: var(--primary-color);
            color: #333;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .sidebar-link {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #c2c7d0;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-link i { width: 30px; font-size: 1.1rem; }

        /* Hiệu ứng khi active hoặc hover */
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff !important;
            border-left: 4px solid var(--primary-color);
        }
        
        .sidebar-link.active i {
            color: var(--primary-color);
        }

        .menu-header {
            padding: 20px 20px 5px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #8e949a;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* MAIN CONTENT AREA */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            width: calc(100% - var(--sidebar-width));
        }

        /* TOPBAR */
        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 30px;
            border-bottom: 1px solid #dee2e6;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-full-name {
            font-weight: 700;
            color: #212529;
        }

        .container-fluid { padding: 30px; }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="fas fa-chart-pie"></i></div>
        <span>HỆ THỐNG QUẢN LÝ GIÁ</span>
    </div>

    <div class="mt-3">
        <a href="/dashboard" class="sidebar-link {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        
        <div class="menu-header">Hệ thống</div>
        
        @if(Auth::user()->vai_tro == 'admin')
        <a href="/users" class="sidebar-link {{ Request::is('users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Người dùng
        </a>
        @endif

        <a href="/suppliers" class="sidebar-link {{ Request::is('suppliers*') ? 'active' : '' }}">
            <i class="fas fa-truck-moving"></i> Nhà cung cấp
        </a>

        <a href="/categories" class="sidebar-link {{ Request::is('categories*') ? 'active' : '' }}">
            <i class="fas fa-th-list"></i> Danh mục
        </a>

        <a href="/products" class="sidebar-link {{ Request::is('products*') ? 'active' : '' }}">
            <i class="fas fa-pills"></i> Sản phẩm
        </a>

        <div class="menu-header">Báo cáo & Nghiệp vụ</div>

        <a href="/prices" class="sidebar-link {{ Request::is('prices') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Báo giá
        </a>

        <a href="/prices/compare" class="sidebar-link {{ Request::is('prices/compare*') ? 'active' : '' }}">
            <i class="fas fa-balance-scale"></i> So sánh giá
        </a>
        <a href="/prices/alert" class="sidebar-link {{ Request::is('prices/alert*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Cảnh báo
        </a>

        <div class="menu-header">Tài khoản</div>
        <a href="/profile" class="sidebar-link {{ Request::is('profile*') ? 'active' : '' }}">
            <i class="fas fa-user-lock"></i> Mật khẩu
        </a>

        <form method="POST" action="/logout" id="logout-form">
            @csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="sidebar-link text-warning">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </form>
    </div>
</nav>

<main class="main-content">
    <header class="topbar">
        <div class="user-greeting">
            <span class="user-full-name">
                👋 Chào, {{ Auth::user()->ho_ten }}
            </span>
            <div class="text-muted small">| {{ date('d/m/Y') }}</div> </div>
    </header>

    <div class="container-fluid">
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>