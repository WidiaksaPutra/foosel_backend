<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

use function PHPUnit\Framework\isTrue;

class ProductCategoriesController extends Controller
{
    public function fetchCategories(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit');
        $show_product = $request->input('show_product');

        if($id){
            $categories = ProductCategory::with('products')->find($id);
            if($categories){
                return ResponseFormatter::success(
                    $categories,
                    'data berhasil dipanggil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    404,
                    'data tidak berhasil ditampilkan'
                );
            }
        }

        $categories = ProductCategory::query();
        if($name){
            $categories->where('name', 'like', '%' . $name . '%');
        }

        if($show_product == 'true'){
            $categories->with('products');
        }

        return ResponseFormatter::success(
            $categories->paginate($limit),
            'data berhasil dipanggil'
        );
    }
}
