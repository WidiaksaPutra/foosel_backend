<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductGalleries;
use App\Models\ProductCategory;
use App\Models\TransactionItem;

class Product extends Model{
    use HasFactory, SoftDeletes;

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */

    protected $fillable = [
        'token_id',
        'email',
        'name',
        'price',
        'description',
        'tags',
        'categories_id',
        'url_image',
    ];

    protected $dates = ['deleted_at'];

    public function user(){
        return $this->belongsTo(User::class,'email','email');
    }

    public function galleries(){
        return $this->hasMany(ProductGalleries::class,'token_id_product', 'token_id');
        //bertujuan untuk menghubungkan relasi dari parent ke child
    }

    public function category(){
        return $this->belongsTo(ProductCategory::class,'categories_id','id');
        //bertujuan untuk menghubungkan relasi dari child ke parent
        //bisa juga untuk many to one
        //belongs = milik
    }

    // public function transaction(){
    //     return $this->belongsTo(Transaction::class,'transactions_id','products_id');
    // }
}
