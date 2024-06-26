<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

class ResupplyTransaction extends Model
{
    use HasFactory;

    protected $table = 'resupply_transactions';
    protected $fillable = [
        'id_store',
        'id_user',
        'qty',
        'total_payment',
        'status',
        'note',
    ];

    public static function search($query)
    {
        return self::join('users', 'resupply_transactions.id_user', '=', 'users.id')
            ->join('stores', 'resupply_transactions.id_store', '=', 'stores.id')
            ->join('status_transactions', 'resupply_transactions.status', '=', 'status_transactions.id')
            ->where('users.name', 'like', "%{$query}%")
            ->orWhere('stores.name', 'like', "%{$query}%")
            ->with(['statusTransaction', 'user', 'store'])
            ->select('resupply_transactions.*') // Pilih kolom dari resupply_transactions untuk menghindari konflik
            ->get();
    }


    public function statusTransaction()
    {
        return $this->belongsTo(StatusTransaction::class, 'status', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'id_store', 'id');
    }
}
