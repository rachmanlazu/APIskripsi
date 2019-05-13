<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class produk extends Model
{
    protected $fillable = [
        'jenis_produk', 'nama_produk', 'harga_produk', 'stok'
    ];
}
