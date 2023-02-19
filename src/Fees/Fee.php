<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

interface Fee
{
    public function calculate(float $amount): float;
}
