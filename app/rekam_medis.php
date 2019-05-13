<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class rekam_medis extends Model
{
    protected $fillable = [
        'status', 'pasien_id', 'perawatan_id'
    ];

    public function perawatan(){
        return $this->hasMany(perawatan::class);
    }

    public function pasien(){
        return $this->belongsTo(pasien::class);
    }
}
