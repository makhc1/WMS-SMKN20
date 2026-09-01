<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundTransaction extends Model
{
    protected $fillable = [
        'item_id',
        'transaction_date',
        'quantity',
        'recipient',
        'destination',
        'notes',
        'customer_name',
        'customer_address',
        'courier',
        'estimated_delivery_date',
        'receipt_id',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
