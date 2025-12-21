<?php

namespace App\Modules\Support\Repositories;

use App\Modules\Support\Models\TicketMessage;

class TicketMessageRepository
{
    public function create(array $data): TicketMessage
    {
        return TicketMessage::create($data);
    }
}
