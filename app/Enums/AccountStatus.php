<?php

namespace App\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'ACTIVE';
    case BLOCKED = 'BLOCKED';
    case CLOSED = 'CLOSED';
}
