<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

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

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'item_location')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function pickingLists()
    {
        return $this->belongsToMany(PickingList::class, 'picking_list_item')
            ->withPivot('quantity', 'location_id', 'status')
            ->withTimestamps();
    }
}
