<?php

namespace App\Enums;

enum UserTypes: string
{
    case ADMIN = 'a';
    case INDIVIDUAL = 'i';
    case BUSINESS = 'b';
}
