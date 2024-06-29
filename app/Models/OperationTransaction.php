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

    public static function search($query)
    {
        return self::with(['purchaseTransaction.user', 'purchaseTransaction.customer', 'resupplyTransaction.user', 'resupplyTransaction.store'])
            ->whereHas('purchaseTransaction', function ($queryBuilder) use ($query) {
                $queryBuilder->whereHas('user', function ($userQuery) use ($query) {
                    $userQuery->where('name', 'like', "%{$query}%")
                              ->orWhere('phone', 'like', "%{$query}%");
                })
                ->orWhereHas('customer', function ($customerQuery) use ($query) {
                    $customerQuery->where('name', 'like', "%{$query}%")
                                  ->orWhere('phone', 'like', "%{$query}%");
                });
            })
            ->orWhereHas('resupplyTransaction', function ($queryBuilder) use ($query) {
                $queryBuilder->whereHas('user', function ($userQuery) use ($query) {
                    $userQuery->where('name', 'like', "%{$query}%")
                              ->orWhere('phone', 'like', "%{$query}%");
                })
                ->orWhereHas('store', function ($storeQuery) use ($query) {
                    $storeQuery->where('name', 'like', "%{$query}%")
                               ->orWhere('phone', 'like', "%{$query}%");
                });
            })
            ->get();
    }

    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class, 'id_transaction', 'id');
    }

    public function resupplyTransaction()
    {
        return $this->belongsTo(ResupplyTransaction::class, 'id_transaction', 'id');
    }
    public function categoryTransaction()
    {
        return $this->belongsTo(CategoryTransaction::class, 'id_category_transaction', 'id');
    }
}
