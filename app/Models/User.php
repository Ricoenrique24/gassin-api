<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'role',
        'apikey',
        'token_fcm',
    ];
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function search($query)
    {
        return self::where('role', 'employee')
                    ->where(function($q) use ($query) {
                        $q->where('name', 'like', "%$query%")
                          ->orWhere('username', 'like', "%$query%")
                          ->orWhere('email', 'like', "%$query%")
                          ->orWhere('phone', 'like', "%$query%");
                    })
                    ->get();
    }

    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class, 'id_user', 'id');
    }

    public function resupplyTransactions()
    {
        return $this->hasMany(ResupplyTransaction::class, 'id_user', 'id');
    }
}
