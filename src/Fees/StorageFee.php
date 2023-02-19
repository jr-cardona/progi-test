<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class StorageFee implements Fee
{
    public function calculate(float $amount): float
    {
        if ($amount === 0.0) {
            return 0;
        }

        return 100;
    }
}
