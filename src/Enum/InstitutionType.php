<?php

namespace App\Enum;

enum InstitutionType: string
{
    case College = 'college';
    case Polytechnic = 'polytechnic';
    case School = 'school';
    case University = 'university';

    public function label(): string
    {
        return match($this) {
            self::College => 'College',
            self::Polytechnic => 'Polytechnic',
            self::School => 'School',
            self::University => 'University',
        };
    }
}
