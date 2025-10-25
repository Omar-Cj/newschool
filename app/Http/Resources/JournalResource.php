<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'branch' => $this->branch,
            'description' => $this->description,
            'status' => $this->status,
            'school_id' => $this->school_id,
            'total_collected' => $this->total_collected,
            'transferred_amount' => $this->transferred_amount,
            'progress_percentage' => $this->progress_percentage,
            'remaining_balance' => $this->remaining_balance,
            'is_fully_transferred' => $this->isFullyTransferred(),
            'can_be_closed' => $this->canBeClosed(),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'payment_method_breakdown' => $this->when(
                $request->routeIs(['journals.show', 'cash-transfers.show']),
                $this->getPaymentMethodBreakdown()
            ),
        ];
    }
}
