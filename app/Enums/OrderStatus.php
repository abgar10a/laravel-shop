<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DELIVERING = 'delivering';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
