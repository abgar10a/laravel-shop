<?php

namespace App\Enums;

enum ArticleProcessType: string
{
    case ORDERED = 'ordered';
    case ORDER_CANCELLED = 'order_cancelled';
    case ADDED = 'added';
    case INCREASED = 'increased';
    case DECREASED = 'decreased';
    case REMOVED = 'removed';
}
