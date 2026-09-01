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
        if ($inboundTransaction->status === 'completed') {
            $item = Item::find($inboundTransaction->item_id);
            if ($item) {
                $item->increment('quantity', $inboundTransaction->quantity);
            }
        }
    }

    /**
     * Handle the InboundTransaction "updated" event.
     */
    public function updated(InboundTransaction $inboundTransaction): void
    {
        $item = Item::find($inboundTransaction->item_id);
        if (!$item) {
            return;
        }

        $oldStatus = $inboundTransaction->getOriginal('status');
        $newStatus = $inboundTransaction->status;
        $oldQuantity = (int) $inboundTransaction->getOriginal('quantity');
        $newQuantity = (int) $inboundTransaction->quantity;

        if ($oldStatus === 'pending' && $newStatus === 'completed') {
            // Status changed from pending to completed: add inbound quantity
            $item->increment('quantity', $newQuantity);
        } elseif ($oldStatus === 'completed' && $newStatus === 'pending') {
            // Status changed from completed to pending: deduct previously added quantity
            $item->decrement('quantity', $oldQuantity);
        } elseif ($newStatus === 'completed' && $inboundTransaction->isDirty('quantity')) {
            // Quantity changed while status remains completed: adjust difference
            $diff = $newQuantity - $oldQuantity;
            if ($diff > 0) {
                $item->increment('quantity', $diff);
            } elseif ($diff < 0) {
                $item->decrement('quantity', abs($diff));
            }
        }
    }

    /**
     * Handle the InboundTransaction "deleted" event.
     */
    public function deleted(InboundTransaction $inboundTransaction): void
    {
        if ($inboundTransaction->status === 'completed') {
            $item = Item::find($inboundTransaction->item_id);
            if ($item) {
                $item->decrement('quantity', $inboundTransaction->quantity);
            }
        }
    }

    /**
     * Handle the InboundTransaction "restored" event.
     */
    public function restored(InboundTransaction $inboundTransaction): void
    {
        if ($inboundTransaction->status === 'completed') {
            $item = Item::find($inboundTransaction->item_id);
            if ($item) {
                $item->increment('quantity', $inboundTransaction->quantity);
            }
        }
    }

    /**
     * Handle the InboundTransaction "force deleted" event.
     */
    public function forceDeleted(InboundTransaction $inboundTransaction): void
    {
        //
    }
}
