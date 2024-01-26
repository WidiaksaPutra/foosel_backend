<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Str;

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
        $limit = $request -> input('limit');
        $emailPenjual = $request -> input('email_penjual');
        $emailPembeli = $request -> input('email_pembeli');
        $transaksi = Transaction::with(['products', 'usersPenjual', 'category']);
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
            $transaksi->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function fetchTransaksiPenjual(Request $request){
        $token_id = $request -> input('transactions_id');
        $limit = $request -> input('limit');
        $emailPenjual = $request -> input('email_penjual');
        $emailPembeli = $request -> input('email_pembeli');
        $transaksi = Transaction::with(['products', 'usersPembeli', 'category']);
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
            $transaksi->orderBy('created_at', 'DESC')->simplepaginate($limit),
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
}