<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

final readonly class StorageFee
{
    public function __construct(
        private float $fee = 100,
    ) {
    }

    public function getFee(): float
    {
        return $this->fee;
    }
}
