<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';

    protected $fillable = [
        'ma_san_pham',
        'ma_nha_cung_cap',
        'gia_nhap',
        'gia_ban',
        'gia_thi_truong',
        'thoi_gian_cap_nhat'
    ];

    public $timestamps = false; // vì bạn không dùng created_at
}