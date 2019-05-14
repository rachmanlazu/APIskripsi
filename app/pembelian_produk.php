<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pembelian_produk extends Model
{
    protected $fillable = [
        'pasien_id', 'nama_produk', 'jumlah'
    ];
}
