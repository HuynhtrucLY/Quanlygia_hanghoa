<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;

// 🔥 thêm
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PriceHistoryExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize,
    WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('price_history as h')
            ->join('products as p', 'p.ma_san_pham', '=', 'h.ma_san_pham')
            ->join('suppliers as s', 's.ma_nha_cung_cap', '=', 'h.ma_nha_cung_cap');

        // 🔥 filter
        if ($this->request->ma_san_pham) {
            $query->where('h.ma_san_pham', $this->request->ma_san_pham);
        }

        if ($this->request->ma_nha_cung_cap) {
            $query->where('h.ma_nha_cung_cap', $this->request->ma_nha_cung_cap);
        }

        return $query->select(
            'p.ten_san_pham',
            's.ten_nha_cung_cap',
            'h.thoi_gian_thay_doi',
            'h.gia_nhap_cu',
            'h.gia_nhap_moi',
            'h.gia_thi_truong_luc_do'
        )
        ->orderByDesc('h.thoi_gian_thay_doi')
        ->get();
    }

    // 🔥 HEADER
    public function headings(): array
    {
        return [
            'Sản phẩm',
            'Nhà cung cấp',
            'Thời gian',
            'Giá nhập cũ',
            'Giá nhập mới',
            'Giá thị trường',
            'Mức biến động'
        ];
    }

    // 🔥 DATA
    public function map($row): array
    {
        $diff = $row->gia_nhap_moi - $row->gia_nhap_cu;

        if ($diff > 0) {
            $delta = '▲ +' . number_format($diff);
        } elseif ($diff < 0) {
            $delta = '▼ ' . number_format($diff);
        } else {
            $delta = 'Không đổi';
        }

        return [
            $row->ten_san_pham,
            $row->ten_nha_cung_cap,
            date('d/m/Y H:i', strtotime($row->thoi_gian_thay_doi)),
            number_format($row->gia_nhap_cu),
            number_format($row->gia_nhap_moi),
            number_format($row->gia_thi_truong_luc_do),
            $delta
        ];
    }

    // 🔥 STYLE
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // dòng header
                'font' => ['bold' => true, 'size' => 12],
            ],
        ];
    }
}