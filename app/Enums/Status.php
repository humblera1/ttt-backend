<?php

namespace App\Enums;

enum Status: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public static function values(): array
    {
        return array_map(fn (Status $case) => $case->value, Status::cases());
    }
}

