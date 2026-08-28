<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickingList extends Model
{
    protected $fillable = [
        'code',
        'status',
        'notes',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'picking_list_item')
            ->withPivot('quantity', 'location_id', 'status')
            ->withTimestamps();
    }

    public function getLocationNameAttribute()
    {
        return $this->items->pluck('location.name')->unique()->implode(', ');
    }
}
