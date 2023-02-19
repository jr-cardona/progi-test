<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class SpecialFee implements Fee
{
    public function __construct(private float $percent = 0.02)
    {
    }

    public function calculate(float $amount): float
    {
        return round($amount * $this->percent, 2);
    }

    public function getPercent(): float
    {
        return $this->percent;
    }
}
