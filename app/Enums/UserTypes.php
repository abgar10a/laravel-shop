<?php

namespace App\Enums;

enum UserTypes: string
{
    case ADMIN = 'a';
    case INDIVIDUAL = 'i';
    case BUSINESS = 'b';
    case BUSINESS_VIP = 'bv';

    public static function businessTypes(): array
    {
        return [
            self::BUSINESS->value,
            self::BUSINESS_VIP->value
        ];
    }
}
