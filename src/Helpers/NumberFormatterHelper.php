<?php declare(strict_types=1);

namespace Juanri\ProgiTest\Helpers;

class NumberFormatterHelper
{
    public static function format(float $number): float
    {
        return (float) number_format(
            num: $number,
            decimals: 2,
            thousands_separator: ''
        );
    }
}