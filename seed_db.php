<?php

require_once('pdo_config.php');

$total = intval($argv[1]);
$minPrice = intval($argv[2]);
$maxPrice = intval($argv[3]);

$stmt = $pdo->prepare("SET @p0=?; SET @p1=?; SET @p2=?; CALL `InsertRand`(@p0, @p1, @p2);");
$stmt->execute([$total,$minPrice,$maxPrice]);
$addedProductsTotal = $stmt->rowCount();
echo "Successfully added " . $addedProductsTotal . " products, with price range " . $minPrice . "-" . $maxPrice;
