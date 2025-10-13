<?php

namespace App\Enums;

enum Period: string
{
    case LastMonth = 'last_month';
    case LastSixMonth = 'last_six_month';
    case LastYear = 'last_year';
    case MoreThanYearAgo = 'more_than_year_ago';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return [
            self::LastMonth->value => 'Last Month',
            self::LastSixMonth->value => 'Last Six Month',
            self::LastYear->value => 'Last Year',
            self::MoreThanYearAgo->value => 'More Than Year Ago',
        ];
    }
}
