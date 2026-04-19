<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
{
    $keyword = trim($request->keyword);

    $suppliers = Supplier::query()
        ->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('ten_nha_cung_cap', 'like', "%{$keyword}%")
                  ->orWhere('so_dien_thoai', 'like', "%{$keyword}%");
            });
        })
        ->orderByDesc('ma_nha_cung_cap')
        ->paginate(10);

    return view('suppliers.index', compact('suppliers', 'keyword'));
}

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'ten_nha_cung_cap' => 'required',
            'so_dien_thoai' => 'nullable'
        ]);

        Supplier::create($request->all());

        return redirect('/suppliers')->with('success', 'Thêm nhà cung cấp thành công');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_nha_cung_cap' => 'required',
            'so_dien_thoai' => 'nullable'
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return redirect('/suppliers')->with('success', 'Cập nhật thành công');
    }

    public function delete($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return redirect('/suppliers')->with('success', 'Xóa thành công');
        } catch (\Exception $e) {
            return redirect('/suppliers')->with('error', 'Không thể xóa nhà cung cấp');
        }
    }
}