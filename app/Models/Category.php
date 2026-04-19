<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'ma_danh_muc';
    public $timestamps = false;

    protected $fillable = ['ten_danh_muc'];  
}
