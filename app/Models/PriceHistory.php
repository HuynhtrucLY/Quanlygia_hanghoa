<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table = 'price_history';

    public $timestamps = false;

    protected $fillable = [
        'ma_san_pham',
        'ma_nha_cung_cap',
        'gia_nhap_cu', 
        'gia_nhap_moi',
        'gia_ban_cu',
        'gia_ban_moi',
        'gia_thi_truong_luc_do', 
        'thoi_gian_thay_doi'
    ];

    // Ép kiểu để tính toán chính xác
    protected $casts = [
        'gia_nhap_cu' => 'float',
        'gia_nhap_moi' => 'float',
        'thoi_gian_thay_doi' => 'datetime',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'ma_san_pham', 'ma_san_pham');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'ma_nha_cung_cap', 'ma_nha_cung_cap');
    }
}