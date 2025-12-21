<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Modules\Support\Requests\CreateTicketRequest;
use App\Modules\Support\Requests\ReplyTicketRequest;
use App\Modules\Support\Services\TicketService;
use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Resources\TicketResource;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(protected TicketService $service) {}

    /**
     * عرض جميع التذاكر.
     * - العميل يرى تذاكره فقط.
     * - الموظفون يرون كل التذاكر.
     */
    public function index()
    {
        $user = Auth::user();

        $tickets = $user->hasRole('Client')
            ? Ticket::with('messages')->where('user_id', $user->id)->get()
            : Ticket::with('messages')->get();

        return TicketResource::collection($tickets);
    }

    /**
     * إنشاء تذكرة جديدة (Client فقط)
     */
    public function store(CreateTicketRequest $request)
    {
        $ticket = $this->service->createTicket($request->validated(), $request->user()->id);

        return ApiResponse::sendResponse(201, 'Ticket created successfully', new TicketResource($ticket));
    }

    /**
     * الرد على تذكرة
     */
    public function reply(Ticket $ticket, ReplyTicketRequest $request)
    {
        $user = $request->user();

        // تحقق الصلاحية
        if ($user->hasRole('Client') && $ticket->user_id !== $user->id) {
            return ApiResponse::sendError('Unauthorized', 403);
        }

        $message = $this->service->replyToTicket($ticket, $user->id, $request->message);

        return ApiResponse::sendResponse(200, 'Reply sent successfully', $message);
    }

    /**
     * إغلاق التذكرة
     */
    public function close(Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->hasRole('Client') && $ticket->user_id !== $user->id) {
            return ApiResponse::sendError('Unauthorized', 403);
        }

        $this->service->closeTicket($ticket);

        return ApiResponse::sendResponse(200, 'Ticket closed successfully');
    }

    /**
     * عرض تفاصيل تذكرة واحدة
     */
    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        if ($user->hasRole('Client') && $ticket->user_id !== $user->id) {
            return ApiResponse::sendError('Unauthorized', 403);
        }

        return new TicketResource($ticket->load('messages'));
    }
}
