<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_category_transaction',
        'id_transaction',
        'id_user',
        'total_payment',
        'note',
        'verified',
    ];

    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'id_transaction');
    }

    public function resupplyTransaction()
    {
        return $this->belongsTo(ResupplyTransaction::class, 'id_transaction');
    }
}
