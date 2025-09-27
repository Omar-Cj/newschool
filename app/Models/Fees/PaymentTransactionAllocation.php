<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransactionAllocation extends BaseModel
{
    protected $fillable = [
        'payment_transaction_id',
        'fees_collect_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    public function feesCollect(): BelongsTo
    {
        return $this->belongsTo(FeesCollect::class, 'fees_collect_id');
    }
}
