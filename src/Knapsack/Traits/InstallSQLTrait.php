<?php

namespace Knapsack\Traits;

trait InstallSQLTrait {

    private $pdo;
    
    private $tableName;

    protected function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function setTableName($name)
    {
        $this->tableName = $name;
    }

    public function install()
    {
        if(!$this->isInstalled()) {
            $sql = file_get_contents('install.sql');
            $sql = str_replace("%items%", $this->tableName, $sql);
            $this->pdo->exec($sql);
        }
    }

    public function isInstalled()
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM products LIMIT 1");
            return true;
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        }
        return false;
    }
}
