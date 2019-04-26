<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $fillable = [
        'pasien_id', 'nomor_antrian', 
        'tanggal', 'status'
    ];

    public function pasien(){
        return $this->belongsTo(Pasien::class);
    }

}
