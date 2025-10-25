<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CashTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'journal' => [
                'id' => $this->journal->id,
                'name' => $this->journal->name,
                'display_name' => $this->journal->display_name,
                'branch' => $this->journal->branch,
                'status' => $this->journal->status,
            ],
            'amount' => $this->amount,
            'formatted_amount' => $this->getFormattedAmount(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_badge_class' => $this->getStatusBadgeClass(),
            'notes' => $this->notes,
            'payment_method_breakdown' => $this->when(
                $request->routeIs('cash-transfers.show'),
                $this->payment_method_breakdown
            ),
            'transferred_by' => [
                'id' => $this->transferredBy->id,
                'name' => $this->transferredBy->name,
            ],
            'approved_by' => $this->when($this->approved_by, function () {
                return [
                    'id' => $this->approvedBy->id,
                    'name' => $this->approvedBy->name,
                ];
            }),
            'approved_at' => $this->approved_at?->toISOString(),
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
