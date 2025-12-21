<?php

namespace App\Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
