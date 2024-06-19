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
        return self::where('id_customer', 'like', "%{$query}%")
            ->orWhere('id_user', 'like', "%{$query}%")
            ->orWhere('qty', 'like', "%{$query}%")
            ->orWhere('total_payment', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhere('note', 'like', "%{$query}%")
            ->with('statusTransaction')
            ->get();
    }

    public function statusTransaction()
    {
        return $this->belongsTo(StatusTransaction::class, 'status', 'status');
    }
}
