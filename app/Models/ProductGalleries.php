<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductGalleries extends Model{
    use HasFactory, SoftDeletes;

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $fillable = [
        'token_id_galleries',
        'token_id_product',
        'url',
    ];

    public function products(){
        return $this->belongsTo(Product::class,'token_id_product','token_id');
    }

    // public function getUrlAttribute($url){
    //     return config('app.url') . Storage::url($url);
    //     //tujuannya agar data varchar, dapat benar benar menjadi url lengkap dari http hingga domain
    // }
}
