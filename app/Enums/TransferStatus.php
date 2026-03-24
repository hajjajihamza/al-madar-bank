<?php

namespace App\Enums;

enum TransferStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
}
