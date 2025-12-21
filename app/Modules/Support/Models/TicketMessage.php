<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TicketMessage extends Model
{
    use LogsActivity;

    protected $table = 'ticket_messages';

    protected $fillable = ['ticket_id', 'user_id', 'message'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ticket_replies')
            ->logOnly(['message'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
