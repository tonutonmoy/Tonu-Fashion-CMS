<?php

namespace App\Enums;

enum SupportMessageSender: string
{
    case Customer = 'customer';
    case Admin = 'admin';
}
