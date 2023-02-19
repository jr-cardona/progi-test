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

    private float $calculatedBasicFeeAmount = 0;

    private float $calculatedAssociateFeeAmount = 0;

    private float $calculatedSpecialFeeAmount = 0;

    private float $calculatedTotalPrice = 0;

    private float $calculatedStoragePrice = 0;

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

        return [
            'budget' => $this->budget,
            'maximum_vehicle_amount' => $maxVehicleAmount,
            'fees' => [
                'basic' => $this->calculatedBasicFeeAmount,
                'special' => $this->calculatedSpecialFeeAmount,
                'association' => $this->calculatedAssociateFeeAmount,
                'storage' => $this->calculatedStoragePrice,
            ],
            'total_price' => $this->calculatedTotalPrice,
        ];
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
        $this->calculatedBasicFeeAmount = $this->basicFee->calculate($amount);
        $this->calculatedAssociateFeeAmount = $this->associateFee->calculate($amount);
        $this->calculatedSpecialFeeAmount = $this->specialFee->calculate($amount);
        $this->calculatedStoragePrice = $this->storageFee->getFee();

        $this->calculatedTotalPrice = round(
            $amount
            + $this->calculatedStoragePrice
            + $this->calculatedBasicFeeAmount
            + $this->calculatedAssociateFeeAmount
            + $this->calculatedSpecialFeeAmount,
            2
        );

        return $this->calculatedTotalPrice;
    }

    private function getUpperBoundEquation(): float
    {
        $minStorageFee = $this->storageFee->getFee();
        $minBasicFee = $this->basicFee->calculate(0);
        $minAssociateFee = $this->associateFee->calculate(0);
        $specialFeePercent = $this->specialFee->getPercent();
        return ($this->budget - $minStorageFee - $minBasicFee - $minAssociateFee) / (1 + $specialFeePercent);
    }
}
