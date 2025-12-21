<?php

namespace App\Modules\Customer\Repositories;

use App\Modules\Customer\Models\Ticket;

class TicketRepository
{
    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function updateStatus(Ticket $ticket, string $status): void
    {
        $ticket->update(['status' => $status]);
    }
}
