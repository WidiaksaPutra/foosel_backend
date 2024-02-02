<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller{
    public function insertTransaksi(Request $request){
        try {
            $request->validate([
                'users_email_pembeli' => ['string', 'max:255'],
                'users_email_penjual' => ['string', 'max:255'],
                'products_id' => ['string', 'max:255'],
                'category_id' => ['string', 'max:255'],
                'total' => ['string', 'max:255'],
                'total_price' => ['string', 'max:255'],
                'shipping_price' => ['string', 'max:255'],
                'quantity' => ['string', 'max:255'],
                'status' => ['string', 'max:255'],
            ]);
            $transactionId = Str::random(32).time();
            Transaction::create([
                'transactions_id' => $transactionId,
                'users_email_pembeli' => $request->users_email_pembeli,
                'users_email_penjual' => $request->users_email_penjual,
                'products_id' => $request->products_id,
                'category_id' => $request->category_id,
                'total' => $request->total,
                'total_price' => $request->total_price,
                'shipping_price' => $request->shipping_price,
                'quantity'=> $request->quantity,
                'status' => $request->status,
            ]);
            return ResponseFormatter::success(
                'data berhasil disimpan',
            );
        } catch (Exception $error) {
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

    public function updateTransaction(Request $request){
        try {
            $transactions_id = $request -> input('transactions_id');
            $status = $request -> input('status');

            if($transactions_id){
                Transaction::where('transactions_id', $transactions_id)->update([
                    'status' => $status,
                ]);
            }

            return ResponseFormatter::success(
                'data berhasil diupdate',
            );
        } catch (Exception $error) {
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

    public function fetchTransaksiPembeli(Request $request){
        $token_id = $request -> input('transactions_id');
        $emailPenjual = $request -> input('email_penjual');
        $emailPembeli = $request -> input('email_pembeli');
        
        $transaksi = DB::table('transaction')
        ->leftJoin('products', 'transaction.products_id', '=', 'products.token_id')
        ->leftJoin('users', 'transaction.users_email_penjual', '=', 'users.email')
        ->leftJoin('product_categories', 'transaction.category_id', '=', 'product_categories.id')
        ->select('transaction.*'
        ,'products.name as products_name', 'products.url_image as products_urlImage', 'products.price as product_price', 'products.description as products_description'
        ,'product_categories.name as product_categories_name'
        ,'users.name as users_name_penjual', 'users.username as users_username_penjual', 'users.phone as users_phone_penjual', 'users.roles as users_roles_penjual', 'users.alamat as users_alamat_penjual', 'users.profile_photo_path as users_photo_penjual')
        ->orderBy('transaction.created_at', 'desc');

        if($token_id){
            $transaksi->where('transactions_id', $token_id);
        }
        if($emailPenjual){
            $transaksi->where('users_email_penjual', $emailPenjual);
        }
        if($emailPembeli){
            $transaksi->where('users_email_pembeli', $emailPembeli);
        }
        
        return ResponseFormatter::success(
            $transaksi->get(),
            'data berhasil dipanggil'
        );
        // $transaksi = Transaction::with(['products', 'usersPenjual', 'category']);
        // if($token_id){
        //     $transaksi->where('transactions_id', $token_id);
        // }
        // if($emailPenjual){
        //     $transaksi->where('users_email_penjual', $emailPenjual);
        // }
        // if($emailPembeli){
        //     $transaksi->where('users_email_pembeli', $emailPembeli);
        // }
        // return ResponseFormatter::success(
        //     $transaksi->orderBy('created_at', 'DESC')->simplepaginate($limit),
        //     'data berhasil dipanggil'
        // );
    }

    public function fetchTransaksiPenjual(Request $request){
        $token_id = $request -> input('transactions_id');
        $emailPenjual = $request -> input('email_penjual');
        $emailPembeli = $request -> input('email_pembeli');
        
        $transaksi = DB::table('transaction')
        ->leftJoin('products', 'transaction.products_id', '=', 'products.token_id')
        ->leftJoin('users', 'transaction.users_email_pembeli', '=', 'users.email')
        ->leftJoin('product_categories', 'transaction.category_id', '=', 'product_categories.id')
        ->select('transaction.*'
        ,'products.name as products_name', 'products.url_image as products_urlImage', 'products.price as product_price', 'products.description as products_description'
        ,'product_categories.name as product_categories_name'
        ,'users.name as users_name_pembeli', 'users.username as users_username_pembeli', 'users.phone as users_phone_pembeli', 'users.roles as users_roles_pembeli', 'users.alamat as users_alamat_pembeli','users.profile_photo_path as users_photo_pembeli')
        ->orderBy('transaction.created_at', 'desc');
        
        if($token_id){
            $transaksi->where('transactions_id', $token_id);
        }
        if($emailPenjual){
            $transaksi->where('users_email_penjual', $emailPenjual);
        }
        if($emailPembeli){
            $transaksi->where('users_email_pembeli', $emailPembeli);
        }
        
        return ResponseFormatter::success(
            $transaksi->get(),
            'data berhasil dipanggil'
        );
    }

    public function deleteTransaksi(Request $request){
        try {
            $token_id = $request->input('transactions_id');
            if ($token_id) {
                Transaction::where('transactions_id', $token_id)->forceDelete();
            }
            return ResponseFormatter::success(
                null
                , 'delete product berhasil'
            );
        } catch (Exception $error) {
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
}