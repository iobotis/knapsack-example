<?php

echo "Please choose: \n";
echo "1. Find exact collections.\n";
echo "2. Find close upper limit collections.\n";
echo "Type 1 or 2.\n";

$handle = fopen("php://stdin", "r");
$option = intval(fgets($handle));

$stmt = $pdo->prepare("CALL `get30products`(@p0, @p1); SELECT @p0 AS `str`, @p1 AS `remainingPrice`;");

$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

$remainingPrice = $result['remainingPrice'];

if($option == 1) {
    $stmt = $pdo->prepare("CALL `findProductWithExactPrice`(?, @p1); SELECT @p0 AS `str`, @p1 AS `productId`;");

    $stmt->execute([$remainingPrice]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result['productId'] == 0) {
        echo "Not Found collection\n";
    }
    else {
        echo 'Found collection of ' . $result['str'] . ',' . $result['productId'] . "\n";
    }
}
elseif($option == 2) {
    $stmt = $pdo->prepare("CALL `findProductClosestToPrice`(?, @p1); SELECT @p0 AS `str`, @p1 AS `productId`;");

    $stmt->execute([$remainingPrice]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo 'Found collection of ' . $result['str'] . ',' . $result['productId'] . "\n";
}



