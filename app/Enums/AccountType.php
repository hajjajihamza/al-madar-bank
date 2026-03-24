<?php

namespace App\Enums;

enum AccountType: string
{
    case COURANT = 'COURANT';
    case EPARGNE = 'EPARGNE';
    case MINEUR = 'MINEUR';
}
