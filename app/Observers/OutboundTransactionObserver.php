<?php

namespace App\Observers;

use App\Models\OutboundTransaction;
use App\Models\Item;

class OutboundTransactionObserver
{
    /**
     * Archive the item from master barang if its stock has reached zero.
     */
    protected function archiveIfEmpty(Item $item): void
    {
        if ($item->quantity <= 0) {
            $item->delete();
        }
    }

    /**
     * Handle the OutboundTransaction "created" event.
     */
    public function created(OutboundTransaction $outboundTransaction): void
    {
        if ($outboundTransaction->status === 'completed') {
            $item = Item::find($outboundTransaction->item_id);
            if ($item) {
                $item->decrement('quantity', $outboundTransaction->quantity);
                $this->archiveIfEmpty($item);
            }
        }
    }

    /**
     * Handle the OutboundTransaction "updated" event.
     */
    public function updated(OutboundTransaction $outboundTransaction): void
    {
        $item = Item::find($outboundTransaction->item_id);
        if (!$item) {
            return;
        }

        $oldStatus = $outboundTransaction->getOriginal('status');
        $newStatus = $outboundTransaction->status;
        $oldQuantity = (int) $outboundTransaction->getOriginal('quantity');
        $newQuantity = (int) $outboundTransaction->quantity;

        if ($oldStatus === 'pending' && $newStatus === 'completed') {
            // Status changed from pending to completed: deduct outbound quantity
            $item->decrement('quantity', $newQuantity);
            $this->archiveIfEmpty($item);
        } elseif ($oldStatus === 'completed' && $newStatus === 'pending') {
            // Status changed from completed to pending: restore previously deducted quantity
            $item->increment('quantity', $oldQuantity);
        } elseif ($newStatus === 'completed' && $outboundTransaction->isDirty('quantity')) {
            // Quantity changed while status remains completed: adjust difference
            $diff = $newQuantity - $oldQuantity;
            if ($diff > 0) {
                $item->decrement('quantity', $diff);
                $this->archiveIfEmpty($item);
            } elseif ($diff < 0) {
                $item->increment('quantity', abs($diff));
            }
        }
    }

    /**
     * Handle the OutboundTransaction "deleted" event.
     */
    public function deleted(OutboundTransaction $outboundTransaction): void
    {
        if ($outboundTransaction->status === 'completed') {
            $item = Item::find($outboundTransaction->item_id);
            if ($item) {
                $item->increment('quantity', $outboundTransaction->quantity);
            }
        }
    }

    public function restored(OutboundTransaction $outboundTransaction): void
    {
        if ($outboundTransaction->status === 'completed') {
            $item = Item::find($outboundTransaction->item_id);
            if ($item) {
                $item->decrement('quantity', $outboundTransaction->quantity);
            }
        }
    }

    public function forceDeleted(OutboundTransaction $outboundTransaction): void {}
}
