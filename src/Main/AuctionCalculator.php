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

    private float $maximumBid = 0;

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
            $totalBid = $this->calculateTotalBid($midpoint);
            if (number_format(abs($totalBid - $this->budget), 2) < $this->tolerance) {
                $this->maximumBid = $midpoint;
            }
            if ($totalBid > $this->budget) {
                $this->upperBound = $midpoint - $this->tolerance;
            } else {
                $this->maximumBid = $midpoint;
                $this->lowerBound = $midpoint + $this->tolerance;
            }
        }
    }

    private function calculateTotalBid(float $amount): float
    {
        $basicFeeAmount = $this->basicFee->calculate($amount);
        $associateFeeAmount = $this->associateFee->calculate($amount);
        $specialFeeAmount = $this->specialFee->calculate($amount);
        $storageFeeAmount = $this->storageFee->getFee();
        $totalBid = $amount + $basicFeeAmount + $associateFeeAmount + $specialFeeAmount + $storageFeeAmount;

        return round($totalBid, 2);
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
        if ($this->maximumBid > 0) {
            $this->maximumBid = floor($this->maximumBid * 100) / 100;
            return [
                'budget' => $this->budget,
                'maximum_vehicle_amount' => $this->maximumBid,
                'fees' => [
                    'basic' => $this->basicFee->calculate($this->maximumBid),
                    'special' => $this->specialFee->calculate($this->maximumBid),
                    'association' => $this->associateFee->calculate($this->maximumBid),
                    'storage' => $this->storageFee->getFee(),
                ],
                'total_bid' => $this->calculateTotalBid($this->maximumBid),
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
            'total_bid' => 0.0,
        ];
    }
}
