<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    protected $table = 'purchase_transactions';
    protected $fillable = [
        'id_customer',
        'id_user',
        'qty',
        'total_payment',
        'status',
        'note',
    ];

    public static function search($query)
    {
        return self::join('users', 'purchase_transactions.id_user', '=', 'users.id')
            ->join('customers', 'purchase_transactions.id_customer', '=', 'customers.id')
            ->join('status_transactions', 'purchase_transactions.status', '=', 'status_transactions.id')
            ->where('users.name', 'like', "%{$query}%")
            ->orWhere('customers.name', 'like', "%{$query}%")
            ->with(['statusTransaction', 'user', 'customer'])
            ->select('purchase_transactions.*') // Pilih kolom dari purchase_transactions untuk menghindari konflik
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }
}
