<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'code',
        'name',
        'zone_name',
        'storage_type',
        'capacity_percentage',
        'status',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_location')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
