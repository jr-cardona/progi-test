<?php declare(strict_types=1);

namespace Juanri\ProgiTest\Main;

use Juanri\ProgiTest\Helpers\NumberFormatterHelper;

class AuctionCalculator
{
    public static function calculate(float $budget): array
    {
        $basicFeeMin = 10;
        $basicFeeMax = 50;
        $basicFeePercent = 0.10;
        $specialFeePercent = 0.02;
        $storageFee = 100;
        $price1 = 500;
        $price2 = 1000;
        $price3 = 3000;
        $associationFee1 = 5;
        $associationFee2 = 10;
        $associationFee3 = 15;
        $associationFee4 = 20;
        $maxBid = 0.0;
        $maxBidBasicFee = 0.0;
        $maxBidSpecialFee = 0.0;
        $maxBidAssociationFee = 0.0;
        $maxBidStorageFee = 0.0;
        $maxBidTotalCost = 0.0;

        for ($i = $budget - $storageFee; $i >= 0; $i -= 0.01) {
            $i = NumberFormatterHelper::format((float) $i);

            // Calculate fees for current bid amount
            $basicFee = min($basicFeeMax, max($basicFeeMin, $i * $basicFeePercent));
            $specialFee = NumberFormatterHelper::format($i * $specialFeePercent);

            // Determine association fee based on bid amount
            if ($i < 1) {
                $associationFee = 0;
            } elseif ($i <= $price1) {
                $associationFee = $associationFee1;
            } elseif ($i <= $price2) {
                $associationFee = $associationFee2;
            } elseif ($i <= $price3) {
                $associationFee = $associationFee3;
            } else {
                $associationFee = $associationFee4;
            }

            $totalCost = $i + $basicFee + $specialFee + $associationFee + $storageFee;

            if ($totalCost <= $budget && $i > 0) {
                $maxBid = $i;
                $maxBidBasicFee = NumberFormatterHelper::format($basicFee);
                $maxBidSpecialFee = NumberFormatterHelper::format($specialFee);
                $maxBidAssociationFee = NumberFormatterHelper::format($associationFee);
                $maxBidStorageFee = NumberFormatterHelper::format($storageFee);
                $maxBidTotalCost = NumberFormatterHelper::format($totalCost);
                break;
            }
        }

        return [
            'budget' => $budget,
            'maximum_vehicle_amount' => $maxBid,
            'fees' => [
                'basic' => $maxBidBasicFee,
                'special' => $maxBidSpecialFee,
                'association' => $maxBidAssociationFee,
                'storage' => $maxBidStorageFee,
            ],
            'total_price' => $maxBidTotalCost,
        ];
    }
}