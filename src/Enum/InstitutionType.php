<?php

namespace App\Enum;

enum InstitutionType: string
{
    case University = 'university';
    case College = 'college';
    case Polytechnic = 'polytechnic';
    case School = 'school';

    public function label(): string
    {
        return match($this) {
            self::University => 'University',
            self::College => 'College',
            self::Polytechnic => 'Polytechnic',
            self::School => 'School',
        };
    }
}
