<?php

namespace Knapsack;

use Knapsack\Config;

class Statistics
{

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Config
     */
    private $config;

    private $error;

    public function __construct(\PDO $pdo, Config $config)
    {
        $this->setPdo($pdo);
        $this->setConfig($config);
    }

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

    public function getTotalItemsInRange($min, $max)
    {
        $stmt = $this->pdo->prepare(
            "SELECT count(*) FROM " . $this->config->tableName
            . " WHERE price >= :min AND price < :max"
        );
        $stmt->bindParam(':min', $min);
        $stmt->bindParam(':max', $max);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getPricesForItems($items)
    {
        $qMarks = str_repeat('?,', count($items) - 1) . '?';
        $stmt = $this->pdo->prepare(
            "SELECT * FROM " . $this->config->tableName
            . " WHERE id IN ($qMarks)"
        );
        $stmt->execute($items);
        return $stmt->fetchAll(\PDO::FETCH_UNIQUE);
    }

    public function getError()
    {
        return $this->error;
    }
}