<?php

$total = intval($argv[1]);
$minPrice = intval($argv[2]);
$maxPrice = intval($argv[3]);

$stmt = $pdo->prepare("SET @p0='1000'; SET @p1='0'; SET @p2='1000'; CALL `InsertRand`(@p0, @p1, @p2);");
$stmt->execute([$id]);
$addedProductsTotal = $stmt->rowCount();
