<?php

namespace App\Enums;

enum StatusEnum: string
{
    case DONE = 'done';
    case MISSED = 'missed';
    case PENDING = 'pending';
}
