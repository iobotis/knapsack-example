<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knapsack\ExactCollections;
use Knapsack\Config;
use Knapsack\UpperLimitCollections;

require_once('pdo_config.php');

$config = new Config();
$config->tableName = $table;

$knapsackAlgo = new ExactCollections($pdo, $config);

echo "Please choose: \n";
if(!$knapsackAlgo->isInstalled()) {
    echo "Import install script?(y\/n)\n";
    $handle = fopen("php://stdin", "r");
    $option = trim(fgets($handle));
    if($option !== 'y') {
        echo $option;
        return;
    }
    $knapsackAlgo->setInstallScript(__DIR__. '/../install.sql');
    $success = $knapsackAlgo->install();
    if($success === false) {
        var_dump($knapsackAlgo->getError());
    }
}
echo "1. Find exact collections.\n";
echo "2. Find close upper limit collections.\n";
echo "Type 1 or 2.\n";

$handle = fopen("php://stdin", "r");
$option = intval(fgets($handle));

if($option == 1) {
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

    $knapsackAlgo = new UpperLimitCollections($pdo, $config);

    try {
        $result = $knapsackAlgo->findOneCollection();
        echo 'Found collection of ' . $result . "\n";
    }
    catch(Exception $e) {
        echo $e->getMessage();
        echo "Not Found collection\n";
    }
}



