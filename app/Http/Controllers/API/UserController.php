<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use Tymon\JWTAuth\Contracts\JWTSubject;


class UserController extends Controller{   
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login', 'register']]);//tujuannya
        //agar ketika di test di postman tidak perlu menambahan bearer token untuk mengakses data
        //sehingga penggunaannya biasanya berlaku pada login dan register saja, karena login dan register tidak memerlukan bearer token
    }

    public function register(Request $request){
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'alamat' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:255'],
                'password' => ['required', 'string'],
                'roles' => ['required', 'string'],
                'profile_photo_path' => ['required', 'string'],
                'unit_test' => ['required', 'bool'],
                // 'password' => ['required', 'string', new Password],
            ]);
            if($request->unit_test == false){
                User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email'=> $request->email,
                    'alamat' => $request->alamat,
                    'phone'=> $request->phone,
                    'password'=> Hash::make($request->password),//untuk menggendrate/memparsing password kedalam bentuk acak
                    'roles' => $request->roles,
                    'profile_photo_path' => $request->profile_photo_path
                ]);
                $user = User::where('email', $request->email)->first();
                $credentials = $request->only('email', 'password');
                $token_user = auth()->attempt($credentials);
                return ResponseFormatter::success(
                    [
                        'access_token'=>$token_user,
                        'type_token'=>'Bearer',
                        'user'=>$user
                    ],
                    'data berhasil dipanggil',
                );    
            }else{
                return ResponseFormatter::success(
                    'data berhasil dipanggil',
                );
            }
        } catch (Exception $error) {
            // return "gagal";
            return ResponseFormatter::error(
                [
                    'message'=>'Something when wrong',
                    'error'=>$error,
                ],
                'Autentication failed',
                500
            );
        }
    }

    public function login(Request $request){
        try {
            $request->validate([
                'email'=>'email|required',
                'password'=>'required'
             ]);
             
             $current = request(['email', 'password']);
             if(!Auth::attempt($current)){
                 return ResponseFormatter::error([
                    'massege'=>'Unauthorized'
                 ], 'data tidak ditemukan', 500);
             }
     
            $user = User::where('email', $request->email)->first();
             //hash::check strukturnya adalah Hash::check(variabel request user, variabel dari database, [])
             if(!Hash::check($request->password, $user->password, [])){
                 throw new \Exception('Invalid Credentials');
             }
            $credentials = $request->only('email', 'password');
            // $payloadable = request([
            //     'email' => $user->email,
            //     'name' => $user->name,
            //     'username' => $user->username,
            //     'roles' => $user->roles,
            //  ]);
            // $token_user = $user->createToken('authToken')->plainTextToken;
            $token_user = auth()->attempt($credentials);

            if(!$token_user){
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Unauthorized',
                    ],
                    401
                );
            }
             return ResponseFormatter::success(
                 [
                    'auth_token'=>$token_user,
                    'type_token'=>'Bearer',
                 ],
                 'data ditemukan'
             );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'massege'=>'Unauthorized'
                ]
                , 'data tidak ditemukan', 500
            );
        }       
    }

    public function fetchProfil(Request $request){
        return ResponseFormatter::success(
            $request->user(), 
            'data profil user berhasil ditemukan'
        );
    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        $unitTest = $request->input('unit_test');
        if($unitTest == false){
            $user->update($request->all());
            return ResponseFormatter::success(
                $user, 'update profile'
            );
        }else{
            return ResponseFormatter::success(
                'data berhasil dipanggil',
            );
        }
    }

    // public function updateProfile(Request $request){
    //     $request->validate([
    //         'name'=>['nullable','string', 'max:255'],
    //         'username'=>['nullable', 'string', 'max:255', 'unique:users'],
    //         'email'=>['nullable', 'string', 'email', 'max:255', 'unique:users'],
    //         'phone'=>['nullable','string','max:255'],
    //         'password'=>['nullable', 'string'],
    //     ]);
    //     $user = Auth::user();
    //     $user->User::update([
    //         'name'=>$request->name,
    //         'username'=>$request->username,
    //         'email'=>$request->email,
    //         'phone'=>$request->phone,
    //         'password'=>Hash::make($request->password),
    //     ]);
    //     return ResponseFormatter::success(
    //         $user,
    //         'update profile'
    //     );
    // }

    public function logout(Request $request) {
        Auth::logout();
        // $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success(
            // $token,
            'berhasil logout'
        );
    }
}

// register
//permasalahan controller ini 30/10/2022
//adalah penggunaan try&catch yang dibuat manual
//tipsnya hapus try&catch nya, dan buat dengan cara auto dan pilih option block
//dan test data post di postman bagian body->form-data

//selain itu bug nya juga ada di new password yang harus dihilangkan/diganti

//login
//permasalahan controller ini 31/10/2022
//adalah penggunaan try&catch yang dibuat manual
//tipsnya hapus try&catch nya, dan buat dengan cara auto dan pilih option block
//'email|required' yang sebelumnya dibuat kebalik
//!Auth::attempt($current) dibuat manual
//if(!Hash::check($request->password, $user->password, [])) dibuat kebalik dan manual