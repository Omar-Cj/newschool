<?php

namespace App\Events;

use App\Models\CashTransfer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CashTransferApproved
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public CashTransfer $transfer
    ) {}
}
