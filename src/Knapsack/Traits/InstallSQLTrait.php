<?php

namespace Knapsack\Traits;

trait InstallSQLTrait {

    private $pdo;
    
    private $tableName;

    private $error;

    protected function setPdo(\PDO $pdo)
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
            $success = $this->pdo->exec($sql);

            if($success === false) {
                $this->error = $this->pdo->errorInfo();
                return false;
            }
        }
        return true;
    }

    public function isInstalled()
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM products LIMIT 1");
            return true;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            // We got an exception == table not found
            return false;
        }
        return false;
    }

    public function getError()
    {
        return$this->error;
    }
}
