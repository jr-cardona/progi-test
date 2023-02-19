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

    private float $upperBound;

    public function __construct(
        private readonly float $budget,
        private readonly float $tolerance = 0.0001,
        private readonly BasicFee $basicFee = new BasicFee(),
        private readonly AssociateFee $associateFee = new AssociateFee(),
        private readonly SpecialFee $specialFee = new SpecialFee(),
        private readonly StorageFee $storageFee = new StorageFee()
    ) {
        $this->upperBound = $this->getUpperBoundEquation();
    }



    public static function create(float ...$params): self
    {
        return new self(...$params);
    }

    /**
     * @return array<string, array<string, float>|float>
     */
    public function execute(): array
    {
        $maxVehicleAmount = $this->getMaxVehicleAmount();

        return $this->getResponseByMaxVehicleAmount($maxVehicleAmount);
    }

    private function getMaxVehicleAmount(): float
    {
        if ($this->upperBound <= 0) {
            return 0;
        }

        while ($this->lowerBound <= $this->upperBound) {
            $midpoint = (float)($this->lowerBound + $this->upperBound) / 2;
            $totalPrice = $this->calculateTotalPrice($midpoint);

            if (abs($totalPrice - $this->budget) <= $this->tolerance) {
                return round($midpoint, 2);
            }
            if ($totalPrice > $this->budget) {
                $this->upperBound = $midpoint - $this->tolerance;
            } else {
                $this->lowerBound = $midpoint + $this->tolerance;
            }
        }

        return round($this->upperBound, 2);
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

    private function getUpperBoundEquation(): float
    {
        $minStorageFee = $this->storageFee->getFee();
        $minBasicFee = $this->basicFee->calculate(0);
        $minAssociateFee = $this->associateFee->calculate(0);
        $specialFeePercent = $this->specialFee->getPercent();
        return ($this->budget - $minStorageFee - $minBasicFee - $minAssociateFee) / (1 + $specialFeePercent);
    }

    private function getResponseByMaxVehicleAmount(float $maxVehicleAmount): array
    {
        if ($maxVehicleAmount > 0) {
            return [
                'budget' => $this->budget,
                'maximum_vehicle_amount' => $maxVehicleAmount,
                'fees' => [
                    'basic' => $this->basicFee->calculate($maxVehicleAmount),
                    'special' => $this->specialFee->calculate($maxVehicleAmount),
                    'association' => $this->associateFee->calculate($maxVehicleAmount),
                    'storage' => $this->storageFee->getFee(),
                ],
                'total_price' => $this->calculateTotalPrice($maxVehicleAmount),
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
