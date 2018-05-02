<?php

require_once('pdo_config.php');
require_once('KnapsackAlgorithm.php');

$knapsackAlgo = new KnapsackAlgorithm($pdo);

echo "Please choose: \n";
if(!$knapsackAlgo->isInstalled()) {
    echo "0. Import install script.\n";
}
echo "1. Find exact collections.\n";
echo "2. Find close upper limit collections.\n";
echo "Type 1 or 2.\n";

$handle = fopen("php://stdin", "r");
$option = intval(fgets($handle));

if($option == 0) {
    $knapsackAlgo->install();
}
elseif($option == 1) {
    try {
        $result = $knapsackAlgo->findOneCollection();
        echo 'Found collection of ' . $result . "\n";
    }
    catch(Exception $e) {
        echo $e->getMessage();
        echo "Not Found collection\n";
    }
}
elseif($option == 2) {
    $stmt = $pdo->prepare("CALL `findProductClosestToPrice`(?, @p1); SELECT @p0 AS `str`, @p1 AS `productId`;");

    $stmt->execute([$remainingPrice]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo 'Found collection of ' . $result['str'] . ',' . $result['productId'] . "\n";
}



