<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\PriceHistory;
use App\Exports\PriceHistoryExport;



class PriceController extends Controller
{
    // DANH SÁCH GIÁ
  public function index(Request $request)
{
    $query = Price::join('products', 'prices.ma_san_pham', '=', 'products.ma_san_pham')
        ->join('suppliers', 'prices.ma_nha_cung_cap', '=', 'suppliers.ma_nha_cung_cap')
        ->select(
            'prices.id',
            'prices.ma_san_pham',
            'prices.ma_nha_cung_cap',
            'products.ten_san_pham',
            'suppliers.ten_nha_cung_cap',
            'suppliers.dia_chi',
            'prices.gia_nhap',
            'prices.gia_ban',
            'prices.gia_thi_truong'
        )
        ->orderBy('products.ten_san_pham', 'asc')
        ->orderBy('suppliers.ten_nha_cung_cap', 'asc');
    if ($request->keyword) {
        $keyword = trim($request->keyword);

        $query->where(function ($q) use ($keyword) {
            $q->where('products.ten_san_pham', 'like', "%{$keyword}%")
              ->orWhere('suppliers.ten_nha_cung_cap', 'like', "%{$keyword}%");
        });
    }

    // lọc theo sản phẩm 
    if ($request->ma_san_pham) {
        $query->where('prices.ma_san_pham', $request->ma_san_pham);
    }

    // lọc theo khu vực 
    if ($request->khu_vuc) {

    $khuVuc = strtolower(trim($request->khu_vuc));
    $khuVuc = str_replace(['.', ' '], '', $khuVuc);

    $query->where(function($q) use ($khuVuc) {
        $q->whereRaw(
            "REPLACE(REPLACE(LOWER(suppliers.dia_chi), '.', ''), ' ', '') LIKE ?",
            ['%' . $khuVuc . '%']
        );
    });
}

    $data = $query->get();
    $products = Product::all();

    $khuVucList = config('khuvuc.list');
    return view('prices.index', compact('data', 'products', 'khuVucList'));
}

    // FORM TẠO MỚI
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('prices.create', compact('products', 'suppliers'));
    }

    // LƯU / CẬP NHẬT GIÁ (TRÁNH TRÙNG)
    public function store(Request $request)
{
    // CHUẨN HÓA % (10 hoặc 10%)
    $loi_nhuan = str_replace('%', '', $request->loi_nhuan);
    $loi_nhuan = (float) trim($loi_nhuan);
    $request->merge([
        'loi_nhuan' => $loi_nhuan
    ]);
    $request->validate([
        'ma_san_pham' => 'required',
        'ma_nha_cung_cap' => 'required',
        'gia_nhap' => 'required|numeric|min:1', 
        'loi_nhuan' => 'required|numeric|min:0', 
        'gia_thi_truong' => 'required|numeric|min:1',
    ], [
        'gia_nhap.required' => 'Vui lòng nhập giá nhập.',
        'gia_nhap.min' => 'Giá nhập phải lớn hơn 0.',
        'gia_nhap.numeric' => 'Giá nhập phải là một con số.',
        'loi_nhuan.required' => 'Vui lòng nhập % lợi nhuận.',
        'loi_nhuan.min' => '% lợi nhuận không được nhỏ hơn 0.',
        'gia_thi_truong.required' => 'Vui lòng nhập giá thị trường.',
        'gia_thi_truong.min' => 'Giá thị trường phải lớn hơn 0.',
    ]);

    DB::beginTransaction();

    try {
        // CHECK TRÙNG
        $exists = Price::where('ma_san_pham', $request->ma_san_pham)
            ->where('ma_nha_cung_cap', $request->ma_nha_cung_cap)
            ->exists();

        if ($exists) {
            DB::rollBack();
            return back()->with('error', '❌ Báo giá đã tồn tại!');
        }
        $gia_nhap = (float) $request->gia_nhap;
        $gia_thi_truong = (float) $request->gia_thi_truong;

        // TÍNH GIÁ BÁN
        $gia_ban = $gia_nhap + ($gia_nhap * $loi_nhuan / 100);

        // INSERT PRICE
        Price::create([
            'ma_san_pham' => $request->ma_san_pham,
            'ma_nha_cung_cap' => $request->ma_nha_cung_cap,
            'gia_nhap' => $gia_nhap,
            'gia_ban' => $gia_ban,
            'gia_thi_truong' => $gia_thi_truong,
            'thoi_gian_cap_nhat' => now()
        ]);

        // INSERT HISTORY
        DB::table('price_history')->insert([
            'ma_san_pham' => $request->ma_san_pham,
            'ma_nha_cung_cap' => $request->ma_nha_cung_cap,
            'gia_nhap_cu' => 0,
            'gia_nhap_moi' => $gia_nhap,
            'gia_ban_cu' => 0,
            'gia_ban_moi' => $gia_ban,
            'gia_thi_truong_luc_do' => $gia_thi_truong,
            'thoi_gian_thay_doi' => now(),
        ]);

        DB::commit();

        return redirect('/prices')->with('success', '✔ Thêm mới thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

    // FORM SỬA
    public function edit(int $id)
    {
        $price = Price::findOrFail($id);
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('prices.edit', compact('price', 'products', 'suppliers'));
    }


public function update(Request $request, int $id)
{
    $loi_nhuan = str_replace('%', '', $request->loi_nhuan);
    $loi_nhuan = (float) trim($loi_nhuan);

    $request->merge([
        'loi_nhuan' => $loi_nhuan
    ]);

    $request->validate([
        'gia_nhap' => 'required|numeric|min:1',
        'loi_nhuan' => 'required|numeric|min:0',
        'gia_thi_truong' => 'required|numeric|min:1',
    ], [
        'gia_nhap.min' => 'Giá nhập phải lớn hơn 0.',
        'gia_thi_truong.min' => 'Giá thị trường phải lớn hơn 0.',
        'loi_nhuan.min' => 'Lợi nhuận không được âm.',
        'loi_nhuan.numeric' => 'Lợi nhuận phải là số (VD: 10 hoặc 10%).',
    ]);

    DB::beginTransaction();

    try {
        $price = Price::findOrFail($id);

        // 3. CHUẨN HÓA SỐ (fix luôn lỗi dấu chấm)
        $new_gia_nhap = (float) str_replace('.', '', $request->gia_nhap);
        $new_gia_thi_truong = (float) str_replace('.', '', $request->gia_thi_truong);

        // dùng lại đã xử lý ở trên
        $loi_nhuan = $request->loi_nhuan;

        // 4. TÍNH GIÁ BÁN
        $new_gia_ban = $new_gia_nhap + ($new_gia_nhap * $loi_nhuan / 100);

        // 5. DỮ LIỆU CŨ
        $old_gia_nhap = (float) $price->gia_nhap;
        $old_gia_ban = (float) $price->gia_ban;
        $old_gia_thi_truong = (float) $price->gia_thi_truong;

        //  6. KHÔNG THAY ĐỔI
        if (
            $old_gia_nhap == $new_gia_nhap &&
            $old_gia_thi_truong == $new_gia_thi_truong &&
            round($old_gia_ban) == round($new_gia_ban)
        ) {
            return redirect('/prices')->with('info', 'Không có thay đổi nào.');
        }

        //  7. UPDATE
        $price->update([
            'gia_nhap' => $new_gia_nhap,
            'gia_ban' => $new_gia_ban,
            'gia_thi_truong' => $new_gia_thi_truong,
            'thoi_gian_cap_nhat' => now(),
        ]);

        //  8. HISTORY
        $lastHistory = DB::table('price_history')
            ->where('ma_san_pham', $price->ma_san_pham)
            ->where('ma_nha_cung_cap', $price->ma_nha_cung_cap)
            ->orderByDesc('thoi_gian_thay_doi')
            ->first();

        if (
            !$lastHistory ||
            $lastHistory->gia_nhap_moi != $new_gia_nhap ||
            $lastHistory->gia_ban_moi != $new_gia_ban
        ) {
            DB::table('price_history')->insert([
                'ma_san_pham' => $price->ma_san_pham,
                'ma_nha_cung_cap' => $price->ma_nha_cung_cap,
                'gia_nhap_cu' => $old_gia_nhap,
                'gia_nhap_moi' => $new_gia_nhap,
                'gia_ban_cu' => $old_gia_ban,
                'gia_ban_moi' => $new_gia_ban,
                'gia_thi_truong_luc_do' => $new_gia_thi_truong,
                'thoi_gian_thay_doi' => now(),
            ]);
        }

        DB::commit();
        return redirect('/prices')->with('success', '✔ Cập nhật thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
}

    //  XEM LỊCH SỬ GIÁ
public function getPriceHistory(int $productId, int $supplierId)
{
    $history = DB::table('price_history as h')
        ->join('products as p', 'p.ma_san_pham', '=', 'h.ma_san_pham')
        ->join('suppliers as s', 's.ma_nha_cung_cap', '=', 'h.ma_nha_cung_cap')
        ->where('h.ma_san_pham', $productId)
        ->where('h.ma_nha_cung_cap', $supplierId)
        ->select(
            'p.ten_san_pham',
            's.ten_nha_cung_cap',
            'h.ma_san_pham', 
            'h.ma_nha_cung_cap', 
            'h.gia_nhap_cu',
            'h.gia_nhap_moi',
            'h.gia_ban_cu',
            'h.gia_ban_moi',
            'h.gia_thi_truong_luc_do',
            'h.thoi_gian_thay_doi'
        )
        ->orderByDesc('h.thoi_gian_thay_doi')
        ->get();

    return view('prices.history_modal', compact('history'));
}
public function alert()
{
    $alerts = DB::table('prices')
        ->join('products', 'products.ma_san_pham', '=', 'prices.ma_san_pham')
        ->join('suppliers', 'suppliers.ma_nha_cung_cap', '=', 'prices.ma_nha_cung_cap')
        ->select(
            'products.ten_san_pham',
            'suppliers.ten_nha_cung_cap',
            'prices.gia_nhap',
            'prices.gia_ban',
            'prices.gia_thi_truong'
        )
        ->get()
        ->filter(function ($item) {

            //  1. BÁN CAO HƠN THỊ TRƯỜNG (mất cạnh tranh)
            if ($item->gia_ban > $item->gia_thi_truong) {
                return true;
            }

            //  2. NHẬP CAO HƠN THỊ TRƯỜNG (nguồn hàng xấu)
            if ($item->gia_nhap > $item->gia_thi_truong) {
                return true;
            }

            //  3. BIÊN LỢI NHUẬN QUÁ THẤP (< 5%)
            $profit = $item->gia_ban - $item->gia_nhap;
            $percent = ($item->gia_nhap > 0)
                ? ($profit / $item->gia_nhap) * 100
                : 0;

            if ($percent < 5) {
                return true;
            }

            return false;
        });

    return view('prices.alert', compact('alerts'));
}


public function compare(Request $request)
{
    $productId = $request->ma_san_pham;
    $khuVuc = $request->khu_vuc;
    $type = $request->type ?? 'supplier';

    // =========================
    // CASE 1: THEO NHÀ CUNG CẤP (GIÁ HIỆN TẠI)
    // =========================
    if ($type == 'supplier') {

        $query = DB::table('prices as p')
            ->join('products as pr', 'p.ma_san_pham', '=', 'pr.ma_san_pham')
            ->join('suppliers as s', 'p.ma_nha_cung_cap', '=', 's.ma_nha_cung_cap')
            ->select(
                'p.ma_san_pham',
                'p.ma_nha_cung_cap',
                'pr.ten_san_pham',
                's.ten_nha_cung_cap',
                's.dia_chi',
                'p.gia_nhap',
                'p.gia_ban',
                'p.gia_thi_truong'
            );

        // lọc
        if ($productId) {
            $query->where('p.ma_san_pham', $productId);
        }

        if ($khuVuc) {
            $query->where('s.dia_chi', 'like', '%' . $khuVuc . '%');
        }

        $latestPrices = $query
            ->orderBy('pr.ten_san_pham')
            ->orderBy('s.ten_nha_cung_cap')
            ->get();
    }

    // =========================
    // CASE 2: THEO THỜI GIAN (HISTORY)
    // =========================
    else {

        $query = DB::table('price_history as h')
            ->join('products as pr', 'h.ma_san_pham', '=', 'pr.ma_san_pham')
            ->join('suppliers as s', 'h.ma_nha_cung_cap', '=', 's.ma_nha_cung_cap')
            ->select(
                'h.ma_san_pham',
                'h.ma_nha_cung_cap',
                'pr.ten_san_pham',
                's.ten_nha_cung_cap',
                's.dia_chi',
                'h.gia_nhap_moi as gia_nhap',
                'h.gia_ban_moi as gia_ban',
                'h.gia_thi_truong_luc_do as gia_thi_truong',
                'h.thoi_gian_thay_doi'
            );

        // lọc sản phẩm
        if ($productId) {
            $query->where('h.ma_san_pham', $productId);
        }

        // lọc khu vực
        if ($khuVuc) {
            $query->where('s.dia_chi', 'like', '%' . $khuVuc . '%');
        }

        //  lọc thời gian
        if ($type == 'month' && $request->month) {

            $query->whereMonth('h.thoi_gian_thay_doi', date('m', strtotime($request->month)))
                  ->whereYear('h.thoi_gian_thay_doi', date('Y', strtotime($request->month)));

        } elseif ($type == 'range' && $request->from_date && $request->to_date) {

            $query->whereBetween('h.thoi_gian_thay_doi', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);

        } elseif ($type == 'quarter' && $request->quarter) {

            $year = date('Y');
            $q = $request->quarter;

            $startMonth = ($q - 1) * 3 + 1;
            $endMonth = $startMonth + 2;

            $query->whereYear('h.thoi_gian_thay_doi', $year)
                  ->whereBetween(DB::raw('MONTH(h.thoi_gian_thay_doi)'), [$startMonth, $endMonth]);
        }

        $latestPrices = $query
            ->orderBy('pr.ten_san_pham')
            ->orderBy('s.ten_nha_cung_cap')
            ->orderByDesc('h.thoi_gian_thay_doi')
            ->get();
    }

    // =========================
    //  GỢI Ý NCC TỐT NHẤT
    // =========================
    $recommend = $latestPrices
        ->map(function ($item) {
            $item->profit = $item->gia_ban - $item->gia_nhap;
            return $item;
        })
        ->filter(function ($item) {
            return $item->gia_nhap < $item->gia_thi_truong
                && $item->profit > 0;
        })
        ->groupBy('ma_san_pham')
        ->map(function ($items) {
            return $items->sortByDesc('profit')->first();
        })
        ->values();

    $products = DB::table('products')->get();
$khuVucList = config('khuvuc.list');

    return view('prices.compare', compact(
        'latestPrices',
        'products',
        'productId',
        'recommend',
        'khuVucList',
        'type'
    ));
}

public function recommend()
{
    $rows = DB::table('prices as p')
        ->join('products as pr', 'pr.ma_san_pham', '=', 'p.ma_san_pham')
        ->join('suppliers as s', 's.ma_nha_cung_cap', '=', 'p.ma_nha_cung_cap')
        ->select(
            'p.ma_san_pham',
            'pr.ten_san_pham',
            's.ten_nha_cung_cap',
            'p.gia_nhap',
            'p.gia_ban',
            'p.gia_thi_truong'
        )
        ->get();

    $result = $rows
        //  BƯỚC 1: tính profit trước
        ->map(function ($item) {
            $item->profit = $item->gia_ban - $item->gia_nhap;
            return $item;
        })

        //  BƯỚC 2: lọc điều kiện chuẩn
        ->filter(function ($item) {
            return $item->gia_nhap < $item->gia_thi_truong
                && $item->profit > 0; 
        })

        //  BƯỚC 3: group theo sản phẩm
        ->groupBy('ma_san_pham')

        //  BƯỚC 4: chọn NCC lời cao nhất
        ->map(function ($items) {
            return $items->sortByDesc('profit')->first();
        })
        ->values();

    return view('prices.compare', [
        'recommend' => $result
    ]);
}
    
public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    DB::beginTransaction();

    try {

        $rows = Excel::toArray([], $request->file('file'))[0];
        unset($rows[0]); // bỏ header

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        foreach ($rows as $row) {

            // =========================
            // 1. CHUẨN HÓA
            // =========================
            $tenSanPham = ucwords(strtolower(trim($row[0] ?? '')));
            $danhMuc    = ucwords(strtolower(trim($row[1] ?? '')));
            $ncc        = ucwords(strtolower(trim($row[2] ?? '')));
            $giaNhap = round(floatval($row[3] ?? 0));
            $giaThiTruong = round(floatval($row[5] ?? 0));

            // =========================
            // 2. LỢI NHUẬN
            // =========================
            $rawLoiNhuan = $row[4] ?? 0;

            if (is_numeric($rawLoiNhuan)) {
                $loiNhuan = ($rawLoiNhuan <= 1)
                    ? $rawLoiNhuan * 100
                    : $rawLoiNhuan;
            } else {
                $loiNhuan = floatval(str_replace('%', '', $rawLoiNhuan));
            }

            // =========================
            // 3. VALIDATE
            // =========================
            if (!$tenSanPham || !$ncc || !$danhMuc || $giaNhap <= 0) {
                $skipped++;
                continue;
            }

            // =========================
            // 4. GIÁ BÁN
            // =========================
            $giaBan = round($giaNhap + ($giaNhap * $loiNhuan / 100));

            // =========================
            // 5. CATEGORY
            // =========================
            $category = Category::firstOrCreate([
                'ten_danh_muc' => $danhMuc
            ]);

            // =========================
            // 6. SUPPLIER
            // =========================
            $supplier = Supplier::whereRaw(
                'LOWER(TRIM(ten_nha_cung_cap)) = ?',
                [strtolower(trim($ncc))]
            )->first();

            if (!$supplier) {
                $supplier = Supplier::create([
                    'ten_nha_cung_cap' => $ncc,
                    'dia_chi' => $row[8] ?? null,
                    'so_dien_thoai' => $row[9] ?? null,
                ]);
            } else {
                $supplier->update([
                    'dia_chi' => $row[8] ?: $supplier->dia_chi,
                    'so_dien_thoai' => $row[9] ?: $supplier->so_dien_thoai,
                ]);
            }

            // =========================
            // 7. PRODUCT
            // =========================
            $product = Product::whereRaw(
                'LOWER(TRIM(ten_san_pham)) = ?',
                [strtolower(trim($tenSanPham))]
            )->first();

            $donViTinh = trim($row[6] ?? '');
            $xuatXu    = trim($row[7] ?? '');

            if (!$product) {
                $product = Product::create([
                    'ten_san_pham' => $tenSanPham,
                    'ma_danh_muc' => $category->ma_danh_muc,
                    'don_vi_tinh' => $donViTinh ?: null,
                    'xuat_xu'     => $xuatXu ?: null,
                ]);
            } else {
                $product->update([
                    'ma_danh_muc' => $category->ma_danh_muc,
                    'don_vi_tinh' => $product->don_vi_tinh ?: $donViTinh,
                    'xuat_xu'     => $product->xuat_xu ?: $xuatXu,
                ]);
            }

            // =========================
            // 8. PRICE
            // =========================
            $price = Price::where('ma_san_pham', $product->ma_san_pham)
                ->where('ma_nha_cung_cap', $supplier->ma_nha_cung_cap)
                ->first();

            // =========================
            // 9. INSERT
            // =========================
            if (!$price) {

                Price::create([
                    'ma_san_pham' => $product->ma_san_pham,
                    'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
                    'gia_nhap' => $giaNhap,
                    'gia_ban' => $giaBan,
                    'gia_thi_truong' => $giaThiTruong,
                    'thoi_gian_cap_nhat' => now()
                ]);

                $inserted++;
                continue;
            }

            // =========================
            // 10. SO SÁNH 
            // =========================
            if (
                intval($price->gia_nhap) == $giaNhap &&
                intval($price->gia_ban) == $giaBan &&
                intval($price->gia_thi_truong) == $giaThiTruong
            ) {
                $skipped++;
                continue;
            }

            // =========================
            // 11. HISTORY
            // =========================
            PriceHistory::create([
                'ma_san_pham' => $product->ma_san_pham,
                'ma_nha_cung_cap' => $supplier->ma_nha_cung_cap,
                'gia_nhap_cu' => $price->gia_nhap,
                'gia_nhap_moi' => $giaNhap,
                'gia_ban_cu' => $price->gia_ban,
                'gia_ban_moi' => $giaBan,
                'gia_thi_truong_luc_do' => $giaThiTruong,
                'thoi_gian_thay_doi' => now()
            ]);

            // =========================
            // 12. UPDATE
            // =========================
            $price->update([
                'gia_nhap' => $giaNhap,
                'gia_ban' => $giaBan,
                'gia_thi_truong' => $giaThiTruong,
                'thoi_gian_cap_nhat' => now()
            ]);

            $updated++;
        }

        DB::commit();

        return back()->with('success',
            "✔ Import thành công!<br>
             ➕ Thêm mới: $inserted<br>
             🔄 Cập nhật: $updated<br>
             ⏭ Bỏ qua: $skipped"
        );

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', '❌ Lỗi import: ' . $e->getMessage());
    }
}
public function exportHistory(Request $request)
{
    return Excel::download(new PriceHistoryExport($request), 'bao_cao_gia' . date('d_m_Y') . '.xlsx');
}
}