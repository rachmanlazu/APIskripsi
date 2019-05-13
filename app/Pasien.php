<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pasien extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'password', 'no_member', 'jenis_kelamin', 
        'usia', 'pekerjaan', 'no_telp', 'alergi', 'tempat_tinggal'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function reservasi(){
        return $this->belongsTo(Reservasi::class);
    }

    public function rekam_medis(){
        return $this->hasMany(rekam_medis::class);
    }

}
