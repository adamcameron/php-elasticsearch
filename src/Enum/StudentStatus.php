<?php

namespace App\Enum;

enum StudentStatus: string
{
    case Active = 'active';
    case Graduated = 'graduated';
    case OnLeave = 'on_leave';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Graduated => 'Graduated',
            self::OnLeave => 'On Leave',
        };
    }
}
