<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Models\Ticket;
use App\Modules\Customer\Models\TicketMessage;
use App\Modules\Customer\Enums\TicketStatus;
use App\Modules\Customer\Repositories\TicketRepository;
use App\Modules\Customer\Repositories\TicketMessageRepository;

class TicketService
{
    public function __construct(
        protected TicketRepository $ticketRepository,
        protected TicketMessageRepository $messageRepository
    ) {}

    public function createTicket(array $data, int $userId): Ticket
    {
        $ticket = $this->ticketRepository->create([
            'user_id' => $userId,
            'subject' => $data['subject'],
            'description'=>$data['description'],
            'priority' => $data['priority'] ?? 'normal',
            'status' => TicketStatus::OPEN->value,
        ]);

        // $this->messageRepository->create([
        //     'ticket_id' => $ticket->id,
        //     'user_id' => $userId,
        //     'message' => $data['description'],
        // ]);

        return $ticket;
    }

    public function replyToTicket(Ticket $ticket, int $userId, string $message): TicketMessage
    {
        if (!$ticket->status === TicketStatus::CLOSED->value) {
            $this->ticketRepository->updateStatus(
                $ticket,
                TicketStatus::IN_PROGRESS->value
            );
        }

        return $this->messageRepository->create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'message' => $message,
        ]);
    }

    public function closeTicket(Ticket $ticket): void
    {
        $this->ticketRepository->updateStatus($ticket,TicketStatus::CLOSED->value);
    }
}
