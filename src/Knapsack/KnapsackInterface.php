<?php

namespace Knapsack;

use Knapsack\Config;

interface KnapsackInterface
{

    public function __construct(\PDO $pdo, Config $config);

    public function findOneCollection();

    public function findMultipleCollections($total = 10);

    public function findAndSaveCollections($total = 10);
}