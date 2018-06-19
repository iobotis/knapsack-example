<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\Config;
use Knapsack\ExactCollections;

require_once('pdo_config.php');

echo "How many products to add?\n";
$handle = fopen("php://stdin", "r");
$total = intval(fgets($handle));

echo "Max price?\n";
$handle = fopen("php://stdin", "r");
$maxPrice = intval(fgets($handle));

$minPrice = 0;

$config = new Config();
$config->tableName = $table;

$knapsackAlgo = new ExactCollections($pdo, $config);

$addedProductsTotal = $knapsackAlgo->seedDb($total,$minPrice,$maxPrice);

echo "Successfully added " . $addedProductsTotal . " products, with price range " . $minPrice . "-" . $maxPrice;
