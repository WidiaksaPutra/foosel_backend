<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductGalleries;
use Illuminate\Support\Facades\File;

class ProductDetail extends Controller
{
    public function fetchProductDetailGuest(Request $request){
        $token_id = $request -> input('token_id');
        $limit = $request -> input('limit');

        $product = Product::with(['category', 'galleries']);
        if($token_id){
            $product -> where('token_id', $token_id);
        }
        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function fetchProductDetailPembeli(Request $request){
        $token_id = $request -> input('token_id');
        $limit = $request -> input('limit');

        $product = Product::with(['category', 'galleries', 'user']);
        if($token_id){
            $product -> where('token_id', $token_id);
        }
        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function fetchProductDetailPenjual(Request $request){
        $token_id = $request -> input('token_id');
        $limit = $request -> input('limit');

        $product = Product::with(['category', 'galleries', 'user']);
        $product->where('email', Auth::user()->email);
        if($token_id){
            $product -> where('token_id', $token_id);
        }
        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function deleteProductDetail(Request $request){
        try {
            $token_id = $request -> input('token_id');
            $imageOld = $request -> input('url_image');
            
            if($imageOld){
                $pathGambarOld = $imageOld;
                if (File::exists($pathGambarOld)) {
                    File::delete($pathGambarOld);
                }
            }

            if ($token_id) {
                $dataGalleries = ProductGalleries::where('token_id_product', $token_id) -> get();
                foreach ($dataGalleries as $data) {
                    $pathDataGalleries = $data->url;
                    if (File::exists($pathDataGalleries)) {
                        File::delete($pathDataGalleries);
                    }
                }
                // Hapus produk berdasarkan token_id secara soft delete
                // penghapusan yang dilakukan tanpa menghapus data dalam tabel 
                // Product::where('token_id', $token_id)->delete();
                // Hapus galeri berdasarkan token_id_product secara soft delete
                // ProductGalleries::where('token_id_product', $token_id)->delete();
                // Penghapusan data forceDelete bersifat permanen karena menghilangkan data pada tabel juga
                Product::where('token_id', $token_id)->forceDelete();
                ProductGalleries::where('token_id_product', $token_id)->forceDelete();
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