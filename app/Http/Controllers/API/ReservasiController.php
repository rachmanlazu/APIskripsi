<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Pasien;
use App\Reservasi;
use App\Perawatan;
use App\rekam_medis;
use App\Produk;
use App\pembelian_produk;
use Illuminate\Support\Facades\Auth;
use Validator;

class ReservasiController extends Controller
{
    public $successStatus = 200;
    public function ambilAntrian(Request $request, Reservasi $reservasi)
    {
        $tanggal_sekarang        = date('Y-m-d');
        $user = Auth::user();
        $jam            = date('H');
        if($user){
            if($jam < 16){
                $cek_antrian = Reservasi::where([['pasien_id', $user->id], ['status', 0], ['tanggal', $tanggal_sekarang]])->first();
                if($cek_antrian){
                    return response()->json([
                        'status'    => $this->successStatus,
                        'success'   => false,
                        'user'      => $user->id,
                        'message'   => 'tidak bisa ambil antrian karena sudah mengambil'
                    ]);
                }

                $jumlah_antrian = Reservasi::where([['tanggal', $tanggal_sekarang], ['status', 0]])->count();

                if($jumlah_antrian == 0){
                    $db_nomor_antrian   = 1;
                    $db_jam             = 9;
                    $db_tanggal         = $tanggal_sekarang;
                    $db_antrian_saat_ini= 0;
                }elseif($jumlah_antrian != 0){
                    $antrian_pertama = Reservasi::where([['tanggal', $tanggal_sekarang], ['status', 0]])->orderBy('created_at', 'asc')->first();

                    $antrian_terakhir = Reservasi::where([['tanggal', $tanggal_sekarang], ['status', 0]])->orderBy('created_at', 'desc')->first();

                    $db_nomor_antrian   = $antrian_terakhir->nomor_antrian + 1;
                    $db_jam             = $antrian_terakhir->jam + 1;
                    $db_tanggal         = $tanggal_sekarang;
                    $db_antrian_saat_ini= $antrian_pertama->nomor_antrian;
                }

                $reservasi   = Reservasi::create([
                    'pasien_id'             => $user->id,
                    'nomor_antrian'         => $db_nomor_antrian,
                    'jam'                   => $db_jam,
                    'tanggal'               => $db_tanggal,
                    'status'                => false
                ]);

                $reservasi = (object) $reservasi;

                $data = [
                    'pasien_id'    => $reservasi->pasien_id,
                    'nomor_antrian'=> $reservasi->nomor_antrian,
                    'jam'          => $reservasi->jam,
                    'tanggal'      => $reservasi->tanggal,
                    'status'       => $reservasi->status,
                    'nomor_antrian_saat_ini' => $db_antrian_saat_ini
                    
                ];

                return response()->json([
                    'status'    => $this->successStatus,
                    'success'   => true,
                    'data'      => $data
                ]);

            }else{
                //DAFTAR BUAT HARI ESOK
                $tanggal_besok = Carbon::now()->addDay()->toDateString();
                $cek_antrian = Reservasi::where([['pasien_id', $user->id], ['status', 0], ['tanggal', $tanggal_besok]])->first();
                if($cek_antrian){
                    return response()->json([
                        'status'    => $this->successStatus,
                        'success'   => false,
                        'user'      => $user->id,
                        'message'   => 'tidak bisa ambil antrian karena sudah mengambil'
                    ]);
                }

                $jumlah_antrian = Reservasi::where([['tanggal', $tanggal_besok], ['status', 0]])->count();

                if($jumlah_antrian == 0){
                    $db_nomor_antrian   = 1;
                    $db_jam             = 9;
                    $db_tanggal         = $tanggal_besok;
                    $db_antrian_saat_ini= 0;
                }elseif($jumlah_antrian != 0){
                    $antrian_pertama = Reservasi::where([['tanggal', $tanggal_besok], ['status', 0]])->orderBy('created_at', 'asc')->first();

                    $antrian_terakhir = Reservasi::where([['tanggal', $tanggal_besok], ['status', 0]])->orderBy('created_at', 'desc')->first();

                    $db_nomor_antrian   = $antrian_terakhir->nomor_antrian + 1;
                    $db_jam             = $antrian_terakhir->jam + 1;
                    $db_tanggal         = $tanggal_besok;
                    $db_antrian_saat_ini= 0;
                }

                $reservasi   = Reservasi::create([
                    'pasien_id'             => $user->id,
                    'nomor_antrian'         => $db_nomor_antrian,
                    'jam'                   => $db_jam,
                    'tanggal'               => $db_tanggal,
                    'status'                => false
                ]);

                $reservasi = (object) $reservasi;

                $data = [
                    'pasien_id'    => $reservasi->pasien_id,
                    'nomor_antrian'=> $reservasi->nomor_antrian,
                    'jam'          => $reservasi->jam,
                    'tanggal'      => $reservasi->tanggal,
                    'status'       => $reservasi->status,
                    'nomor_antrian_saat_ini' => $db_antrian_saat_ini
                    
                ];

                return response()->json([
                    'status'    => $this->successStatus,
                    'success'   => true,
                    'data'      => $data
                ]);
            }
        } 
        
    }

    public function getReservasi(Request $request, Reservasi $reservasi)
    {
        // $user = Auth::user();
        // if($user){
        //     $db_antrian_pertama = Reservasi::select('nomor_antrian')->where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'asc')->first();

        //     return response()->json([
        //         'status'    => $this->successStatus,
        //         'success'   => true,
        //         'data' => [
        //             'jumlah_antrian'=> $jumlah_antrian,
        //             'waktu_tunggu'  => $waktu_tunggu,
        //         ]
        //     ]);


        //}
        $jam  = date('H');
        if($jam > 16){
            return response()->json([
                'status'    => $this->successStatus,
                'success'   => false,
                // 'message'   => 'Mohon maaf reservasi untuk hari ini telah tutup'
                'data' => [
                    'message'       => 'Mohon maaf reservasi untuk hari ini telah tutup',
                    'jumlah_antrian'=> 0,
                    'waktu_tunggu'  => 0
                ]
            ]);
        }else{
            $tanggal        = date('Y-m-d');
            $jumlah_antrian = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->count();
            if($jumlah_antrian){
                $db_antrian_terakhir = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'desc')->first();
                $waktu_tunggu   = $db_antrian_terakhir->jam - $jam;
                return response()->json([
                    'status'    => $this->successStatus,
                    'success'   => true,
                    'data' => [
                        'jumlah_antrian'=> $jumlah_antrian,
                        'waktu_tunggu'  => $waktu_tunggu,
                    ]
                ]);
            }else{
                return response()->json([
                    'status'    => $this->successStatus,
                    'success'   => true,
                    'data' => [
                        'jumlah_antrian'=> $jumlah_antrian,
                        'waktu_tunggu'  => 'tidak ada waktu tunggu'
                    ]
                ]);
            }   
        }
        
    }

    public function getRekamMedis(Request $request, rekam_medis $rekam_medis)
    {
        $user = Auth::user();
        $id_pasien = $user->id;
        
        $cek_history = $user->rekam_medis()->count();
        $history_mediss = $user->rekam_medis()->get();
        if(!$cek_history){
            return response()->json([
                'status'    => $this->successStatus,
                'success'   => true,
                'data'      => 'no history data' 
            ]);
        }else{
            foreach($history_mediss as $history_medis){
                $perawatan = Perawatan::where('id', $history_medis->perawatan_id)->first();
                $data[] = [
                    'tanggal'           => $history_medis->created_at->toDateString(),
                    'perawatan'         => $perawatan->nama_perawatan
                ];
            }
        }
        
        return response()->json([
            'status'    => $this->successStatus,
            'success'   => true,
            'data'      => $data 
        ]);
    }

    public function getProduk(Request $request, Produk $produk)
    {
        $user = Auth::user();
        if($user){
                $produks = Produk::select('id', 'jenis_produk', 'harga_produk', 'nama_produk', 'stok')->get();
            
            
        }
            
        return response()->json([
            'status'    => $this->successStatus,
            'success'   => true,
            'data'       => $produks    
        ]);
    }

    public function detailProduk(Request $request, Produk $produk, $id)
    {
        $user = Auth::user();
        if($user){
           
                $produks = Produk::select('id', 'jenis_produk', 'harga_produk', 'nama_produk', 'stok')->where('id', $id)->first();
           
            
        }
            
        return response()->json([
            'status'    => $this->successStatus,
            'success'   => true,
            'data'       => $produks    
        ]);
    }

    public function getAntrian(Request $request, Reservasi $reservasi)
    {
        $user = Auth::user();
        $tanggal        = date('Y-m-d');
        if($user){
            $antrian_pertama = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'asc')->first();
            
            if(!$antrian_pertama){
                $nomor_antrian_saat_ini = 0;
            }else{
                $nomor_antrian_saat_ini = $antrian_pertama->nomor_antrian;
            }

            // $jumlah_antrian = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->count();

            $antrian_user = Reservasi::where([['tanggal', $tanggal], ['status', 0], ['pasien_id', $user->id]])->orderBy('created_at', 'asc')->first();
            if(!$antrian_user){
                $nomor_antrian = 0;
                $jam    = '';
                $db_tanggal = '';
            }else{
                $nomor_antrian = $antrian_user->nomor_antrian;
                $jam           = $antrian_user->jam;
                $db_tanggal    = $antrian_user->tanggal; 
            }

            // $waktu_tunggu = $antrian_user->jam - $db_antrian_pertama->jam;
        
            return response()->json([
                'status'    => $this->successStatus,
                'success'   => true,
                'data' => [
                    'nomor_antrian_saat_ini'    => $nomor_antrian_saat_ini,
                    'nomor_antrian'=> $nomor_antrian,
                    'jam'  => $jam,
                    'tanggal'=> $db_tanggal
                ]
            ]);
        }
        
    }

    public function getRiwayatPembelian(Request $request)
    {
        $user = Auth::user();
        $id_pasien = $user->id;
        $history_pembelians = pembelian_produk::where('pasien_id', $id_pasien)->get();
        foreach($history_pembelians as $history_pembelian){
            $data[] = [
                'nama_produk'    => $history_pembelian->nama_produk,
                'jumlah'         => $history_pembelian->jumlah,
                'tanggal'        => $history_pembelian->created_at->toDateString()
                
            ];
        }
            
        return response()->json([
            'status'    => $this->successStatus,
            'success'   => true,
            'data'       => $data    
        ]);
    }
}
