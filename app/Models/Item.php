<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sku',
        'name',
        'category',
        'brand',
        'quantity',
        'location',
        'receipt_date',
        'origin',
        'low_stock_threshold',
        'base_price',
        'photo',
        'description',
        'unit',
    ];

    public function inbounds()
    {
        return $this->hasMany(InboundTransaction::class);
    }

    public function outbounds()
    {
        return $this->hasMany(OutboundTransaction::class);
    }
}
