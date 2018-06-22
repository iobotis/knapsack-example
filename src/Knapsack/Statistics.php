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
        // @todo add where clause.
        $result = $this->pdo->query(
            "SELECT count(*) FROM " . $this->config->tableName
            . " WHERE price >= '$min' AND price < '$max'"
        );
        $result->execute();
        return $result->fetchColumn();
    }

    public function getError()
    {
        return $this->error;
    }
}