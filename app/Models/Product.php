<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'ma_san_pham';
    public $timestamps = false;

    protected $fillable = [
    'ten_san_pham',
    'ma_danh_muc',
    'don_vi_tinh',
    'xuat_xu'
    ];
}
