<?php

namespace App\Modules\Transactions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'transaction_reference' => $this->transaction_reference,
            'transaction_type' => $this->transaction_type->value,
            'status' => $this->transaction_status->value,
            'amount' => (float) $this->transaction_amount,
            'currency' => $this->transaction_currency,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'created_by' => $this->created_by_user_id,
            'approved_by' => $this->approved_by_user_id,
            'approved_at' => $this->approved_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            // بحال هي المعاملة مجدولة
            'is_recurring' => (bool) $this->is_recurring,
            'frequency' => $this->frequency?->value,
            'next_run_at' => $this->next_run_at,
        ];
    }
}
