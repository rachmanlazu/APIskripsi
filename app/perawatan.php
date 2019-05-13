<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class perawatan extends Model
{
    protected $fillable = [
        'jenis_perawatan', 'harga_perawatan', 'nama_perawatan'
    ];
    
    public function rekam_medis(){
        return $this->hasMany(rekam_medis::class);
    }
}
