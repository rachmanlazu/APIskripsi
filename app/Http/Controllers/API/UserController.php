<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pasien;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public $successStatus = 200;
    
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] = $user;
            return response()->json(['success' => $success], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function register(Request $request, Pasien $pasien)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $pasien = $pasien->create([
            'nama'  => $request->nama,
            'email' => $request->email,
            'password'  => bcrypt($request->password),
        ]);

        // $input = $request->all();
        
        // $input['password'] = bcrypt($input['password']);
        // $input['nama'] =  $input['nama'];
        // dd($input);
        // $user = Pasien::create($input);
        $success['token'] =  $pasien->createToken('MyApp')->accessToken;
      

        return response()->json(['success'=>$success], $this->successStatus);
    }

    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function details()
    {
        $user = Auth::user();
        // $user = 
        // return response()->json(
        //     ['success' => $user], 
        //     $this->successStatus);
        if($user){
            return response()->json([
                'status' => $this->successStatus,
                'success' => true,
                'data' => [
                    'nama' => $user->nama,
                    'no_member' => $user->no_member,
                    'no_telp' => $user->no_telp,
                    'tempat_tinggal' => $user->tempat_tinggal,
                ],
                
            ]);
        } 
        
    }
}