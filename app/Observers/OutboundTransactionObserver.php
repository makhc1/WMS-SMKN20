<?php

namespace App\Observers;

use App\Models\OutboundTransaction;
use App\Models\Item;

class OutboundTransactionObserver
{
    /**
     * Handle the OutboundTransaction "created" event.
     */
    public function created(OutboundTransaction $outboundTransaction): void
    {
        $item = Item::find($outboundTransaction->item_id);
        if ($item) {
            $item->quantity -= $outboundTransaction->quantity;
            $item->save();
        }
    }

    /**
     * Handle the OutboundTransaction "updated" event.
     */
    public function updated(OutboundTransaction $outboundTransaction): void
    {
        if ($outboundTransaction->isDirty('quantity')) {
            $item = Item::find($outboundTransaction->item_id);
            if ($item) {
                $oldQuantity = $outboundTransaction->getOriginal('quantity');
                $newQuantity = $outboundTransaction->quantity;
                $diff = $newQuantity - $oldQuantity;
                // If it increases, stock goes down. If it decreases, stock goes up.
                $item->quantity -= $diff;
                $item->save();
            }
        }
    }

    /**
     * Handle the OutboundTransaction "deleted" event.
     */
    public function deleted(OutboundTransaction $outboundTransaction): void
    {
        $item = Item::find($outboundTransaction->item_id);
        if ($item) {
            $item->quantity += $outboundTransaction->quantity;
            $item->save();
        }
    }

    public function restored(OutboundTransaction $outboundTransaction): void {}
    public function forceDeleted(OutboundTransaction $outboundTransaction): void {}
}
