<?php declare(strict_types=1);

namespace Juanri\ProgiTest\Fees;

interface HasMinimalFee
{
    public function getMinimalFee(): float;
}