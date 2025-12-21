<?php

namespace App\Modules\Customer\Repositories;

use App\Modules\Customer\Models\TicketMessage;

class TicketMessageRepository
{
    public function create(array $data): TicketMessage
    {
        return TicketMessage::create($data);
    }
}
