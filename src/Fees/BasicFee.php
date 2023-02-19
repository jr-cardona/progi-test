<?php declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class BasicFee implements Fee, HasMinimalFee
{
    public function __construct(
        private float $min = 10,
        private float $max = 50,
        private float $percent = 0.10,
    ){
    }

    public function calculate(float $amount): float
    {
        if ($amount === 0.0) {
            return 0;
        }

        return min($this->max, max($this->min, $amount * $this->percent));
    }

    public function getMinimalFee(): float
    {
        return $this->min;
    }
}