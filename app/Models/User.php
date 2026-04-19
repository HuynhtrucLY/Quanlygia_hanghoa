<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'ma_nguoi_dung';
    public $timestamps = false;

    protected $fillable = [
    'ho_ten',
    'ten_dang_nhap',
    'email',
    'password',
    'vai_tro',
    'trang_thai'
];
}