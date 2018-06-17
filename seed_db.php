<?php

require_once('pdo_config.php');
require_once('KnapsackAlgorithm.php');

echo "How many products to add?\n";
$handle = fopen("php://stdin", "r");
$total = intval(fgets($handle));

echo "Max price?\n";
$handle = fopen("php://stdin", "r");
$maxPrice = intval(fgets($handle));

$minPrice = 0;

$knapsackAlgo = new ExactCollections($pdo);

$addedProductsTotal = $knapsackAlgo->seedDb($total,$minPrice,$maxPrice);

echo "Successfully added " . $addedProductsTotal . " products, with price range " . $minPrice . "-" . $maxPrice;
