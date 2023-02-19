<?php declare(strict_types=1);

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
        private readonly float $tolerance = 0.001,
        private readonly BasicFee $basicFee = new BasicFee(),
        private readonly AssociateFee $associateFee = new AssociateFee(),
        private readonly SpecialFee $specialFee = new SpecialFee(),
        private readonly StorageFee $storageFee = new StorageFee()
    ) {
        $minBasicFee = $this->basicFee->getMinimalFee();
        $minAssociateFee = $this->associateFee->getMinimalFee();
        $specialFeePercent = $this->specialFee->getPercent();

        $this->upperBound = ($this->budget - $minBasicFee - $minAssociateFee) / (1 + $specialFeePercent);
    }



    public static function create(...$params): self
    {
        return new self(...$params);
    }

    public function execute(): array
    {
        $maxVehicleAmount = $this->getMaxVehicleAmount();

        return [
            'budget' => $this->budget,
            'maximum_vehicle_amount' => $maxVehicleAmount,
            'fees' => [
                'basic' => round($this->basicFee->calculate($maxVehicleAmount), 2),
                'special' => round($this->specialFee->calculate($maxVehicleAmount), 2),
                'association' => round($this->associateFee->calculate($maxVehicleAmount), 2),
                'storage' => round($this->storageFee->calculate($maxVehicleAmount), 2),
            ],
            'total_price' => $this->calculateTotalPrice($maxVehicleAmount),
        ];
    }

    private function getMaxVehicleAmount(): float
    {
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
        $storageFeeAmount = $this->storageFee->calculate($amount);
        $totalPrice = $amount + $basicFeeAmount + $associateFeeAmount + $specialFeeAmount + $storageFeeAmount;

        return round($totalPrice, 2);
    }
}