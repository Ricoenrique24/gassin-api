<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusTransaction extends Model
{
    use HasFactory;
    protected $table = 'status_transactions';
    protected $fillable = [
        'status',
        // tambahkan kolom lain yang sesuai
    ];

    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class, 'status', 'status');
    }
    public function resupplyTransactions()
    {
        return $this->hasMany(ResupplyTransaction::class, 'status', 'status');
    }
}
