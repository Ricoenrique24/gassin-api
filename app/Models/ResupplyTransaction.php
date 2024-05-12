<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResupplyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_customer',
        'id_user',
        'qty',
        'total_payment',
        'status',
        'note',
    ];
    
    public function statusTransaction()
    {
        return $this->belongsTo(StatusTransaction::class, 'status', 'status');
    }
}
