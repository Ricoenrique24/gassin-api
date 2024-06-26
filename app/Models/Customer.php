<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = [
        'name',
        'phone',
        'address',
        'link_map',
        'price',
    ];

    public static function search($query)
    {
        return self::where('name', 'like', "%$query%")
                    ->orWhere('phone', 'like', "%$query%")
                    ->orWhere('address', 'like', "%$query%")
                    ->orWhere('link_map', 'like', "%$query%")
                    ->orWhere('price', 'like', "%$query%")
                    ->get();
    }
    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class, 'id_customer', 'id');
    }
}
