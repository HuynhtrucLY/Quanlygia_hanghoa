<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PriceHistory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'ten_dang_nhap' => 'admin',
            'mat_khau' => '123456',
            'vai_tro' => 'admin'
        ]);

        Category::create([
            'ten_danh_muc' => 'Thực phẩm'
        ]);

        Supplier::create([
            'ten_nha_cung_cap' => 'Công ty A',
            'dia_chi' => 'TPHCM',
            'so_dien_thoai' => '0909123456'
        ]);

        Product::create([
            'ten_san_pham' => 'Gạo',
            'ma_danh_muc' => 1,
            'don_vi_tinh' => 'Kg',
            'xuat_xu' => 'Việt Nam'
        ]);
        PriceHistory::create([
            'ma_san_pham' => 1,
            'ma_nha_cung_cap' => 1,
            'gia_nhap' => 10000,
            'gia_thi_truong' => 12000,
            'gia_ban' => 13000,
            'thoi_gian_cap_nhat' => now()
        ]);
    }
}