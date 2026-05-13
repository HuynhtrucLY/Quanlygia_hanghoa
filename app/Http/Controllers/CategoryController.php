<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
    $keyword = trim($request->keyword);

    $categories = Category::query()
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('ten_danh_muc', 'like', "%{$keyword}%");
        })
        ->orderByDesc('ma_danh_muc')
        ->paginate(10);

    return view('categories.index', compact('categories', 'keyword'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_danh_muc' => 'required'
        ]);

        Category::create([
            'ten_danh_muc' => $request->ten_danh_muc
        ]);

        return redirect('/categories')->with('success', 'Thêm danh mục thành công');
    }

    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'ten_danh_muc' => 'required'
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'ten_danh_muc' => $request->ten_danh_muc
        ]);

        return redirect('/categories')->with('success', 'Cập nhật thành công');
    }

    public function delete(int $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return redirect('/categories')->with('success', 'Xóa thành công');
        } catch (\Exception $e) {
            return redirect('/categories')->with('error', 'Danh mục đang có sản phẩm!');
        }
    }
}