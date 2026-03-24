<?php

namespace App\Enums;

enum TransactionType: string
{
    case FEE_FAILED = 'FEE_FAILED';
    case FEE = 'FEE';
    case INTEREST = 'INTEREST';
    case WITHDRAWALL = 'WITHDRAWALL';
    case DEPOSIT = 'DEPOSIT';
}
