<?php

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Checkout;
use App\Http\Controllers\API\ProductDetail;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TransaksiController;
use App\Http\Controllers\API\ProductCategoriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['guest'])->group(function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::get('products', [ProductController::class, 'fetchProduct']);
    Route::get('categories', [ProductCategoriesController::class, 'fetchCategories']);
    Route::get('detailGuest', [ProductDetail::class, 'fetchProductDetailGuest']);
    Route::delete('deleteTransaksi', [TransaksiController::class, 'deleteTransaksi']);
});

Route::middleware(['pembeli'])->group(function () {
    Route::get('productsPembeli', [ProductController::class, 'fetchProductPembeli']);
    Route::get('detailPembeli', [ProductDetail::class, 'fetchProductDetailPembeli']);
    Route::post('insertTransaksi', [TransaksiController::class, 'insertTransaksi']);
    Route::get('fetchTransaksiPembeli', [TransaksiController::class, 'fetchTransaksiPembeli']);
    // Route::delete('deleteTransaksi', [TransaksiController::class, 'deleteTransaksi']);
});

Route::middleware(['penjual'])->group(function () {
    Route::post('insertProduct', [ProductController::class, 'insertProduct']);
    Route::delete('deleteProductDetail', [ProductDetail::class, 'deleteProductDetail']);
    Route::post('updateProduct', [ProductController::class, 'updateProduct']);
    Route::get('productsPenjual', [ProductController::class, 'fetchProductPenjual']);
    Route::get('detailPenjual', [ProductDetail::class, 'fetchProductDetailPenjual']);
    Route::get('fetchTransaksiPenjual', [TransaksiController::class, 'fetchTransaksiPenjual']);
    Route::patch('updateTransaction', [TransaksiController::class, 'updateTransaction']);
    // Route::delete('deleteTransaksi', [TransaksiController::class, 'deleteTransaksi']);
});

Route::middleware(['auth'])->group(function () {//digunakan agar data dap tipanggil menggunakan token JWT
    Route::get('fetchProfil', [UserController::class, 'fetchProfil']);
    Route::patch('updateProfile', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
});

// php artisan optimize
// php artisan cache:clear
// php artisan config:clear
// php artisan make:migration nama_migrasi
// php artisan migrate
// Route::middleware('auth:sanctum')->group(function () {//digunakan agar data dap tipanggil menggunakan token JWT
//     Route::get('fetchProfil',  [UserController::class, 'fetchProfil']);
//     Route::post('updateProfile', [UserController::class,'updateProfile']);
//     Route::post('logout', [UserController::class, 'logout']);
//     Route::get('transaction', [TransactionController::class, 'transaction']);
//     Route::post('checkout', [TransactionController::class, 'checkout']);
// });
