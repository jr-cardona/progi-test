<?php

declare(strict_types=1);

namespace Juanri\ProgiTest\Main;

use Juanri\ProgiTest\Fees\AssociateFee;
use Juanri\ProgiTest\Fees\BasicFee;
use Juanri\ProgiTest\Fees\SpecialFee;
use Juanri\ProgiTest\Fees\StorageFee;

final class AuctionCalculator
{
    private float $lowerBound = 0;

    private float $maximumPrice = 0;

    private float $upperBound;

    public function __construct(
        private readonly float $budget,
        private readonly float $tolerance = 0.0001,
        private readonly BasicFee $basicFee = new BasicFee(),
        private readonly AssociateFee $associateFee = new AssociateFee(),
        private readonly SpecialFee $specialFee = new SpecialFee(),
        private readonly StorageFee $storageFee = new StorageFee()
    ) {
        $this->upperBound = $this->setUpperBoundEquation();
    }

    public function execute(): self
    {
        $this->getMaxVehicleAmount();

        return $this;
    }

    private function getMaxVehicleAmount(): void
    {
        if ($this->upperBound <= 0) {
            return;
        }

        while ($this->lowerBound <= $this->upperBound) {
            $midpoint = (float)($this->lowerBound + $this->upperBound) / 2;
            $totalPrice = $this->calculateTotalPrice($midpoint);
            if (number_format(abs($totalPrice - $this->budget), 2) < $this->tolerance) {
                $this->maximumPrice = $midpoint;
            }
            if ($totalPrice > $this->budget) {
                $this->upperBound = $midpoint - $this->tolerance;
            } else {
                $this->maximumPrice = $midpoint;
                $this->lowerBound = $midpoint + $this->tolerance;
            }
        }
    }

    private function calculateTotalPrice(float $amount): float
    {
        $basicFeeAmount = $this->basicFee->calculate($amount);
        $associateFeeAmount = $this->associateFee->calculate($amount);
        $specialFeeAmount = $this->specialFee->calculate($amount);
        $storageFeeAmount = $this->storageFee->getFee();
        $totalPrice = $amount + $basicFeeAmount + $associateFeeAmount + $specialFeeAmount + $storageFeeAmount;

        return round($totalPrice, 2);
    }

    private function setUpperBoundEquation(): float
    {
        $minStorageFee = $this->storageFee->getFee();
        $minBasicFee = $this->basicFee->calculate(0);
        $minAssociateFee = $this->associateFee->calculate(0);
        $specialFeePercent = $this->specialFee->getPercent();
        return ($this->budget - $minStorageFee - $minBasicFee - $minAssociateFee) / (1 + $specialFeePercent);
    }

    /**
     * @return array<string, array<string, float>|float>
     */
    public function toArray(): array
    {
        if ($this->maximumPrice > 0) {
            $this->maximumPrice = floor($this->maximumPrice * 100) / 100;
            return [
                'budget' => $this->budget,
                'maximum_vehicle_amount' => $this->maximumPrice,
                'fees' => [
                    'basic' => $this->basicFee->calculate($this->maximumPrice),
                    'special' => $this->specialFee->calculate($this->maximumPrice),
                    'association' => $this->associateFee->calculate($this->maximumPrice),
                    'storage' => $this->storageFee->getFee(),
                ],
                'total_price' => $this->calculateTotalPrice($this->maximumPrice),
            ];
        }

        return [
            'budget' => $this->budget,
            'maximum_vehicle_amount' => 0.0,
            'fees' => [
                'basic' => 0.0,
                'special' => 0.0,
                'association' => 0.0,
                'storage' => 0.0,
            ],
            'total_price' => 0.0,
        ];
    }
}
