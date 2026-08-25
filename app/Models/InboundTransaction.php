<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundTransaction extends Model
{
    protected $fillable = [
        'item_id',
        'transaction_date',
        'quantity',
        'supplier',
        'notes',
        'receipt_id',
        'condition',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
