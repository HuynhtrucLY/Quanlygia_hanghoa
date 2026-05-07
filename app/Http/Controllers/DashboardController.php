<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalProducts = DB::table('products')->count();
        $totalSuppliers = DB::table('suppliers')->count();

        // dropdown sản phẩm
        $products = DB::table('products')->get();

        // DÙNG prices (KHÔNG dùng price_history nữa)
        $query = DB::table('prices')
            ->join('products', 'products.ma_san_pham', '=', 'prices.ma_san_pham')
            ->join('suppliers', 'suppliers.ma_nha_cung_cap', '=', 'prices.ma_nha_cung_cap')
            ->select(
                'products.ma_san_pham',
                'products.ten_san_pham',
                'suppliers.ten_nha_cung_cap',
                'prices.gia_nhap',
                'prices.gia_ban',
                'prices.gia_thi_truong'
            );

        // lọc theo sản phẩm
        if ($request->ma_san_pham) {
            $query->where('products.ma_san_pham', $request->ma_san_pham);
        }

        $data = $query->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalSuppliers',
            'data',
            'products'
        ));
    }
}