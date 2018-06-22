<?php

namespace Knapsack\Traits;

use Knapsack\Config;

trait SeedDbTrait {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Config
     */
    private $config;

    protected function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function getTotalItems()
    {
        $result = $this->pdo->query("SELECT count(*) FROM " . $this->config->tableName);
        $result->execute();
        return $result->fetchColumn();
    }

    public function seedDb($total, $minPrice, $maxPrice)
    {
        $stmt = $this->pdo->prepare("CALL `InsertRand`(:total, :minPrice, :maxPrice);");
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':minPrice', $minPrice);
        $stmt->bindParam(':maxPrice', $maxPrice);
        $stmt->execute();
        $stmt->closeCursor();
        return $total;
    }
}
