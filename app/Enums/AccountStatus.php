<?php

namespace App\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case FROZEN = 'frozen';
    case SUSPENDED = 'suspended';
    case CLOSED = 'closed';
}
