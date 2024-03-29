<?php

declare(strict_types=1);

namespace Unit;

use Juanri\ProgiTest\Main\AuctionCalculator;
use PHPUnit\Framework\TestCase;

class VehiclePriceCalculatorTest extends TestCase
{
    /**
     * @param array<string, float> $expectedPrices
     * @dataProvider budgetsDataProvider()
     */
    public function test_auction_calculator_with_given_budget_returns_maximum_amount(array $expectedPrices): void
    {
        $calculatedPrices = (new AuctionCalculator($expectedPrices['budget']))->execute()->toArray();

        $this->assertSame($expectedPrices, $calculatedPrices);
    }

    public function test_auction_calculator_with_loop_of_budgets_does_not_overpass_maximum_amount(): void
    {
        for ($i = 0; $i <= 1000; $i += 0.02) {
            $i = round($i, 2);
            $totalBid =(new AuctionCalculator($i))->execute()->toArray()['total_bid'];
            $this->assertGreaterThanOrEqual($totalBid, $i);
        }
    }

    /**
     * @return array<string, array<array-key, array<string, array<string, float>|float>>>
     */
    public function budgetsDataProvider(): array
    {
        return [
            'Budget $1000.00' => [
                [
                    'budget' => 1000.00,
                    'maximum_vehicle_amount' => 823.53,
                    'fees' => [
                        'basic' => 50.00,
                        'special' => 16.47,
                        'association' => 10.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 1000.00,
                ],
            ],
            'Budget $670.00' => [
                [
                    'budget' => 670.00,
                    'maximum_vehicle_amount' => 500.00,
                    'fees' => [
                        'basic' => 50.00,
                        'special' => 10.00,
                        'association' => 5.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 665.00,
                ],
            ],
            'Budget $670.01' => [
                [
                    'budget' => 670.01,
                    'maximum_vehicle_amount' => 500.01,
                    'fees' => [
                        'basic' => 50.00,
                        'special' => 10.00,
                        'association' => 10.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 670.01,
                ],
            ],
            'Budget $110.00' => [
                [
                    'budget' => 110.00,
                    'maximum_vehicle_amount' => 0.00,
                    'fees' => [
                        'basic' => 0.00,
                        'special' => 0.00,
                        'association' => 0.00,
                        'storage' => 0.00,
                    ],
                    'total_bid' => 0.00,
                ],
            ],
            'Budget $111.00' => [
                [
                    'budget' => 111.00,
                    'maximum_vehicle_amount' => 0.98,
                    'fees' => [
                        'basic' => 10.00,
                        'special' => 0.02,
                        'association' => 0.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 111.00,
                ],
            ],
            'Budget $116.02' => [
                [
                    'budget' => 116.02,
                    'maximum_vehicle_amount' => 1.00,
                    'fees' => [
                        'basic' => 10.00,
                        'special' => 0.02,
                        'association' => 5.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 116.02,
                ],
            ],
            'Budget $1000000.00' => [
                [
                    'budget' => 1000000.00,
                    'maximum_vehicle_amount' => 980225.49,
                    'fees' => [
                        'basic' => 50.00,
                        'special' => 19604.51 ,
                        'association' => 20.00,
                        'storage' => 100.00,
                    ],
                    'total_bid' => 1000000.00,
                ],
            ],
        ];
    }
}
