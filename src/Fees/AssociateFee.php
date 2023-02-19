<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class AssociateFee implements Fee, HasMinimalFee
{
    private int $lastFee;

    public function __construct(
        private array $limits = [1, 500, 1000, 3000],
        private array $fees = [0, 5, 10, 15, 20]
    ) {
        $this->lastFee = end($fees);
    }

    public function calculate(float $amount): float
    {
        if ($amount < $this->limits[0]) {
            return $this->fees[0];
        }

        for ($i = 1; $i < count($this->limits); $i++) {
            if ($amount <= $this->limits[$i]) {
                return $this->fees[$i];
            }
        }

        return $this->lastFee;
    }

    public function getMinimalFee(): float
    {
        return $this->fees[0];
    }
}
