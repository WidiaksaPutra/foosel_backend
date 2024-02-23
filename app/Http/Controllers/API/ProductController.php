<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductGalleries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LengthException;
use PhpParser\Lexer\TokenEmulator\ReverseEmulator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function fetchProduct(Request $request){
        $token_id = $request -> input('token_id');
        $name = $request -> input('name');
        $categories = $request -> input('categories_id');
        $limit = $request -> input('limit');

        $product = Product::with('category');
        if($token_id){
            $product -> where('token_id', $token_id);
        }    
        if($name){
            $product -> where('name', 'like', '%' . $name . '%');
        }
        if($categories){
            $product->where('categories_id', $categories);
        }
        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function fetchProductPembeli(Request $request){
        $token_id = $request -> input('token_id');
        $name = $request -> input('name'); //input('name') artinya menampung input dari user, dengan key 'name',
        //nantinya key 'name' akan dipanggil pada link/url untuk memberikan nilainya, contoh 'name=super' 
        $description = $request -> input('description');
        $tags = $request -> input('tags');
        $categories = $request -> input('categories_id');
        $limit = $request -> input('limit');//memberikan batas maksimal dalam menampilkan data pada api,
        //batas defaultnya 15
        // $show_galleries = $request->input('show_galleries');

        $price_from = $request -> input('price_from');
        $price_to = $request -> input('price_to');
        
        $product = Product::with('category');
        if($token_id){
            $product->where('token_id', $token_id);
        }  

        if($name){
            $product -> where('name', 'like', '%' . $name . '%');
        }

        if($description){
            $product -> where('description', 'like', '%' . $description . '%');
        }

        if($tags){
            $product -> where('tags', 'like', '%' . $tags . '%');
        }

        if($price_from){
            $product -> where('price', '>=', $price_from);
        }

        if($price_to){
            $product -> where('price', '<=', $price_to);
        }

        if($categories){
            $product->where('categories_id', $categories);
        }

        // if($show_galleries == 'true'){
        //     $product->with('galleries');
        // }
        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function fetchProductPenjual(Request $request){
        $token_id = $request -> input('token_id');
        $name = $request -> input('name'); //input('name') artinya menampung input dari user, dengan key 'name',
        $description = $request -> input('description');
        $tags = $request -> input('tags');
        $categories = $request -> input('categories_id');
        $limit = $request -> input('limit');//memberikan batas maksimal dalam menampilkan data pada api,

        $price_from = $request -> input('price_from');
        $price_to = $request -> input('price_to');

        $product = Product::with('category');
        $product->where('email', Auth::user()->email);
        if($token_id){
            $product->where('token_id', $token_id);
        }

        if($name){
            $product -> where('name', 'like', '%' . $name . '%');
        }

        if($description){
            $product -> where('description', 'like', '%' . $description . '%');
        }

        if($tags){
            $product -> where('tags', 'like', '%' . $tags . '%');
        }

        if($price_from){
            $product -> where('price', '>=', $price_from);
        }

        if($price_to){
            $product -> where('price', '<=', $price_to);
        }

        if($categories){
            $product->where('categories_id', $categories);
        }

        return ResponseFormatter::success(
            $product->orderBy('created_at', 'DESC')->simplepaginate($limit),
            'data berhasil dipanggil'
        );
    }

    public function insertProduct(Request $request){
        try {
            $request->validate([
                'email' => ['string', 'email', 'max:255'],
                'name' => ['string', 'max:255'],
                'price' => ['string', 'max:255'],
                'type' => ['string', 'max:255'],
                'description' => ['string', 'max:255'],
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'unit_test' => ['string', 'max:6'],
            ]);
            $images = $request->file('images');
            if($request->unit_test == "false"){
                $token_product = Str::random(32).time();
                $nameImage = $request->email.Str::random(32).time().'.'.$request->image->extension();
                $image = $request->file('image');
                $image->move(public_path('images_cover'), $nameImage);
                $fileImage = "images_cover/".$nameImage;
                Product::create([
                    'token_id' => $token_product,
                    'email' => $request->email,
                    'name' => $request->name,
                    'price' => $request->price,
                    'categories_id' => $request->type,
                    'description' => $request->description,
                    'url_image' => $fileImage,
                ]);   
                if(count($images) >= 1){
                    foreach($images as $img){
                        $token_galleries = Str::random(32).time();
                        $nameImages = $request->email.Str::random(32).time().'.'.$img->getClientOriginalExtension();
                        $img->move(public_path('images_list'), $nameImages);
                        $fileImg = "images_list/".$nameImages;
                        ProductGalleries::create([
                            'token_id_galleries' => $token_galleries,
                            'token_id_product' => $token_product,
                            'url' => $fileImg,
                        ]);
                    }
                }
            }
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

    public function updateProduct(Request $request){
        try {
            $request->validate([
                'token_id' => ['string', 'max:255'], 
                'email' => ['string', 'email', 'max:255'],
                'name' => ['string', 'max:255'],
                'price' => ['string', 'max:255'],
                'type' => ['string', 'max:255'],
                'description' => ['string', 'max:255'],
                'oldImage' => ['string', 'max:255'],
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'unit_test' => ['string', 'max:6'],
            ]);
            
            $images = $request->file('images');
            if($request->token_id && $request->unit_test == false){
                if($request->hasFile('image')){
                    $pathGambarOld = $request->oldImage;
                    if (File::exists($pathGambarOld)) {
                        File::delete($pathGambarOld);
                    }
                    $nameImage = $request->email . Str::random(32) . time() . '.' . $request->image->extension();
                    $fileImage = "images_cover/" . strtolower($nameImage);
                    $request->file('image')->move(public_path('images_cover'), $nameImage);
                }
                $nameImage = $request->email . Str::random(32) . time() . '.' . $request->image->extension();
                $fileImage = "images_cover/" . strtolower($nameImage);
                $request->file('image')->move(public_path('images_cover'), $nameImage);
            }
            if($request->token_id){
                Product::where('token_id', $request->token_id)->update([
                    'email' => $request->email,
                    'name' => $request->name,
                    'price' => $request->price,
                    'categories_id' => $request->type,
                    'description'=> $request->description,
                    'url_image' => $fileImage ?? null,
                ]);
                $dataGalleries = ProductGalleries::where('token_id_product', $request->token_id) -> get();
                foreach ($dataGalleries as $data) {
                    $pathDataGalleries = $data->url;
                    if (File::exists($pathDataGalleries)) {
                        File::delete($pathDataGalleries);
                    }
                }
                ProductGalleries::where('token_id_product', $request->token_id)->forceDelete();
                $images = $request->file('images');
                if(count($images) >= 1){
                    foreach($images as $img){
                        $token_galleries = Str::random(32).time();
                        $nameImages = $request->email.Str::random(32).time().'.'.$img->getClientOriginalExtension();
                        $img->move(public_path('images_list'), $nameImages);
                        $fileImg = "images_list/".$nameImages;
                        ProductGalleries::create([
                            'token_id_galleries' => $token_galleries,
                            'token_id_product' => $request->token_id,
                            'url' => $fileImg,
                        ]);
                    }
                }
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
}
