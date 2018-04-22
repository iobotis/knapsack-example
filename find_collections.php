<?php

require_once('pdo_config.php');
require_once('KnapsackAlgorithm.php');

echo "Please choose: \n";
echo "1. Find exact collections.\n";
echo "2. Find close upper limit collections.\n";
echo "Type 1 or 2.\n";

$handle = fopen("php://stdin", "r");
$option = intval(fgets($handle));

$knapsackAlgo = new KnapsackAlgorithm($pdo);

if($option == 1) {
    try {
        $result = $knapsackAlgo->findOneCollection();
        echo 'Found collection of ' . $result . "\n";
    }
    catch(Exception $e) {
        echo "Not Found collection\n";
    }
}
elseif($option == 2) {
    $stmt = $pdo->prepare("CALL `findProductClosestToPrice`(?, @p1); SELECT @p0 AS `str`, @p1 AS `productId`;");

    $stmt->execute([$remainingPrice]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo 'Found collection of ' . $result['str'] . ',' . $result['productId'] . "\n";
}



