<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TransactionItem;
use App\Models\Transactions;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

   /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'alamat',
        'username',
        'phone',
        'roles',
        'password',
        'profile_photo_path',
    ];
    //protected kebalikan dari public, yaitu method yang hanya dapat diakses didalam atau pada turuannya.
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function product(){
        return $this->hasMany(Product::class,'email','id');
        //bertujuan untuk menghubungkan relasi dari parent ke child
    }
    
    public function transactions(){
        return $this->hasMany(Transaction::class, 'users_email_penjual', 'email');
        //hasMany adalah 1 user dapat mengakses banyak transactions
        //user_id berasal dari table transaction
        //id berasal dari tabel user
    }
    //  @return mixed

   public function getJWTIdentifier()
   {
       return $this->getKey();
   }

   /**
    * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @return array
    */
   public function getJWTCustomClaims()
   {
       return [
        'email' => $this->email,
        'roles' => $this->roles,
       ];
   }
}
