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
        $tanggal        = date('Y-m-d');
        $user = Auth::user();
        if($user){
            $cek_antrian = Reservasi::where([['pasien_id', $user->id], ['status', 0], ['tanggal', $tanggal]])->first();

            if($cek_antrian){
                return response()->json([
                    'status'    => $this->successStatus,
                    'success'   => false,
                    'message'   => 'tidak bisa ambil antrian karena sudah mengambil'
                ]);
            }

            $jam            = date('H');
           
            $jumlah_antrian = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->count();
            
            // ->order_by('upload_time', 'desc')->first()
            if($jumlah_antrian == 0){
                $nomor_antrian = 1;
                $db_antrian_pertama = 0;

                if($jam < 10){
                    $db_jam = 9;    
                }elseif($jam > 17){
                    $tanggal = Carbon::now()->addDay()->toDateString();
                    $jumlah_antrian_nextday = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'desc')->first();
                    $db_jam = $jumlah_antrian_nextday->jam + 1;
                    $nomor_antrian = $jumlah_antrian_nextday->nomor_antrian + 1;
                    
                }else{
                    $db_jam = $jam + 1;
                }
            }else{
                $db_antrian_terakhir = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'desc')->first();
                $db_antrian_pertama = Reservasi::select('nomor_antrian')->where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'asc')->first();
                $nomor_antrian = $jumlah_antrian + 1;
                $db_jam = $db_antrian_terakhir->jam + 1;

                $antrian_saat_ini = $db_antrian_pertama;
            }

            $reservasi   = Reservasi::create([
                'pasien_id'             => $user->id,
                'nomor_antrian'         => $nomor_antrian,
                'jam'                   => $db_jam,
                'tanggal'               => $tanggal,
                'status'                => false
            ]);

            return response()->json([
                'status'    => $this->successStatus,
                'success'   => true,
                'data'      => $reservasi, 
                'nomor_antrian_saat_ini'      => $db_antrian_pertama
            ]);
            
           
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
        $history_mediss = $user->rekam_medis()->get();
        foreach($history_mediss as $history_medis){
            $perawatan = Perawatan::where('id', $history_medis->perawatan_id)->first();
            $data[] = [
                'tanggal'           => $history_medis->created_at->toDateString(),
                'perawatan'         => $perawatan->nama_perawatan
            ];
        }
            
        return response()->json([
            'status'    => $this->successStatus,
            'success'   => true,
            'data'       => $data    
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
            $db_antrian_pertama = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->orderBy('created_at', 'asc')->first();
            
            if(!$db_antrian_pertama){
                $nomor_antrian = 0;
                $jam_antrian_awal = 0;
            }else{
                $nomor_antrian = $db_antrian_pertama->nomor_antrian;
                $jam_antrian_awal = $db_antrian_pertama->jam;
            }

            // $jumlah_antrian = Reservasi::where([['tanggal', $tanggal], ['status', 0]])->count();

            $db_antrian_user = Reservasi::where([['tanggal', $tanggal], ['status', 0], ['pasien_id', $user->id]])->orderBy('created_at', 'asc')->first();

            if(!$db_antrian_user){
                $jam_antrian_akhir = 0;
                $nomor_antrian_user = 0;
            }else{
                $jam_antrian_akhir = $db_antrian_user->jam;
                $nomor_antrian_user = $db_antrian_user->nomor_antrian;
            }

            // $waktu_tunggu = $db_antrian_user->jam - $db_antrian_pertama->jam;
        
            return response()->json([
                'status'    => $this->successStatus,
                'success'   => true,
                'data' => [
                    'nomor_antrian_saat_ini'    => $nomor_antrian,
                    'nomor_antrian'=> $nomor_antrian_user,
                    'jam'  => $jam_antrian_akhir
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
