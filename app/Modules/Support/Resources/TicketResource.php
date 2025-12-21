<?php

namespace App\Modules\Support\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to_user_id,
            'messages' => $this->messages->map(fn($m) => [
                'id' => $m->id,
                'user_id' => $m->user_id,
                'message' => $m->message,
                'created_at' => $m->created_at,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
