<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Pasien;
use App\Reservasi;
use Illuminate\Support\Facades\Auth;
use Validator;

class ReservasiController extends Controller
{
    public $successStatus = 200;
    public function ambilAntrian(Request $request)
    {
        $user = Auth::user();
        
        if($user){
            // $jumlah_antrian = DB::table('reservasis')
            //                 ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            //                 ->whereDate('created_at', '=', date('Y-m-d H:i:s'))
            //                 ->groupBy('hour')
            //                 ->get();
            $jumlah_antrian = Reservasi::where('tanggal', '=', '2019-05-03 15:03:20')->count();
            $tanggal    = date('Y-m-d H:i:s');
            $pasien_id      = $user->id;

            // $reservasi   = Reservasi::create([
            //     'pasien_id'          => $pasien_id,
            //     'nomor_antrian'          => 1,
            //     'tanggal'   => $tanggal,
            //     'status'   => false
            // ]);
            // $date       = date("Y-m-d", strtotime($tanggal));
            // dd($tanggal);

            // return response()->json([
            //     'tanggal' => $tanggal,
            //     'pasien_id'=> $pasien_id,
            //     'jum'       => $jumlah_antrian
            // ]);
            // if($reservasi){
                return response()->json([
                    'status' => $this->successStatus,
                    'success' => true,
                    // 'data' => [
                    //     'pasien_id' => $reservasi->pasien_id,
                    //     'nomor_antrian' => $reservasi->nomor_antrian,
                    //     'tanggal' => $reservasi->tanggal,
                    //     'status' => $reservasi->status
                    // ],
                    'jum' => $jumlah_antrian
                    
                ]);
            // }
           
        } 
        
    }
}
