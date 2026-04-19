<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'ma_nha_cung_cap';
    public $timestamps = false;

    protected $fillable = [
    'ten_nha_cung_cap',
    'dia_chi',
    'so_dien_thoai'
    ];
}
