<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $keyword = trim($request->keyword);

    $products = DB::table('products')
        ->leftJoin('categories', 'products.ma_danh_muc', '=', 'categories.ma_danh_muc')
        ->select(
            'products.*',
            'categories.ten_danh_muc'
        )

        // 🔍 SEARCH
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('products.ten_san_pham', 'like', "%{$keyword}%")
                  ->orWhere('categories.ten_danh_muc', 'like', "%{$keyword}%");
            });
        })

        ->orderByDesc('products.ma_san_pham')
        ->get();

    return view('products.index', compact('products', 'keyword'));
}

    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'ten_san_pham' => 'required',
            'ma_danh_muc' => 'required'
        ]);

        Product::create($request->all());

        return redirect('/products')->with('success', 'Thêm sản phẩm thành công');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = DB::table('categories')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_san_pham' => 'required',
            'ma_danh_muc' => 'required'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect('/products')->with('success', 'Cập nhật thành công');
    }

    public function delete($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return redirect('/products')->with('success', 'Xóa thành công');
        } catch (\Exception $e) {
            return redirect('/products')->with('error', 'Không thể xóa sản phẩm');
        }
    }
}