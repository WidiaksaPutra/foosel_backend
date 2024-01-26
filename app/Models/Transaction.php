<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model{
    use HasFactory, SoftDeletes;

        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $table = 'transaction';

    protected $fillable = [
        'transactions_id',
        'users_email_pembeli',
        'users_email_penjual',
        'products_id',
        'category_id',
        'total',
        'total_price',
        'shipping_price',
        'quantity',
        'status',
    ];

    public function products(){
        return $this -> hasOne(Product::class,'token_id','products_id');
    }

    public function usersPenjual(){
        return $this -> belongsTo(User::class,'users_email_penjual','email');
    }

    public function usersPembeli(){
        return $this -> belongsTo(User::class,'users_email_pembeli','email');
    }

    public function category(){
        return $this->belongsTo(ProductCategory::class,'category_id','id');
    }
}
