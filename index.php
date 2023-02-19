<?php

declare(strict_types=1);

use Juanri\ProgiTest\Main\AuctionCalculator;

require realpath('vendor/autoload.php');

$budget = (float) ($argv[1] ?? readline('Enter a budget: ($)'));

if ($budget <= 0) {
    echo 'Please enter a valid amount';
    return;
}

$maxVehicleBid = (new AuctionCalculator($budget))->execute()->toArray();
echo "\nPresupuesto: $ $budget\n";
echo "Importe del vehículo: $ {$maxVehicleBid['maximum_vehicle_amount']}\n";
echo "Tasa básica del usuario: $ {$maxVehicleBid['fees']['basic']}\n";
echo "Cuota especial del vendedor: $ {$maxVehicleBid['fees']['special']}\n";
echo "Cuota de asociación: $ {$maxVehicleBid['fees']['association']}\n";
echo "Tasa de almacenamiento: $ {$maxVehicleBid['fees']['storage']}\n";
echo "Importe total: $ {$maxVehicleBid['total_bid']}\n";