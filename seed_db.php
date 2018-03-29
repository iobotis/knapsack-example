<?php

$total = intval($argv[1]);

$stmt = $pdo->prepare("CALL get30products(@str,@price); SELECT @str;");
$stmt->execute([$id]);
$name = $stmt->fetchColumn();
