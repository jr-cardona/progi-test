<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class BasicFee implements Fee
{
    public function __construct(
        private float $min = 10,
        private float $max = 50,
        private float $percent = 0.10,
    ) {
    }

    public function calculate(float $amount): float
    {
        return min($this->max, max($this->min, $amount * $this->percent));
    }
}
