<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

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

    public function resupplyTransactions()
    {
        return $this->hasMany(ResupplyTransaction::class, 'id_store', 'id');
    }
}
