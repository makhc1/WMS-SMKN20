<?php

namespace App\Observers;

use App\Models\InboundTransaction;
use App\Models\Item;

class InboundTransactionObserver
{
    /**
     * Handle the InboundTransaction "created" event.
     */
    public function created(InboundTransaction $inboundTransaction): void
    {
        $item = Item::find($inboundTransaction->item_id);
        if ($item) {
            $item->quantity += $inboundTransaction->quantity;
            $item->save();
        }
    }

    /**
     * Handle the InboundTransaction "updated" event.
     */
    public function updated(InboundTransaction $inboundTransaction): void
    {
        // If quantity was changed, adjust stock
        if ($inboundTransaction->isDirty('quantity')) {
            $item = Item::find($inboundTransaction->item_id);
            if ($item) {
                $oldQuantity = $inboundTransaction->getOriginal('quantity');
                $newQuantity = $inboundTransaction->quantity;
                $diff = $newQuantity - $oldQuantity;
                $item->quantity += $diff;
                $item->save();
            }
        }
    }

    /**
     * Handle the InboundTransaction "deleted" event.
     */
    public function deleted(InboundTransaction $inboundTransaction): void
    {
        $item = Item::find($inboundTransaction->item_id);
        if ($item) {
            $item->quantity -= $inboundTransaction->quantity;
            $item->save();
        }
    }

    /**
     * Handle the InboundTransaction "restored" event.
     */
    public function restored(InboundTransaction $inboundTransaction): void
    {
        //
    }

    /**
     * Handle the InboundTransaction "force deleted" event.
     */
    public function forceDeleted(InboundTransaction $inboundTransaction): void
    {
        //
    }
}
