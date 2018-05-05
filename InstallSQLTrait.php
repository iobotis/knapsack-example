<?php

trait InstallSQLTrait {

    private $pdo;

    protected function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function install()
    {
        if(!$this->isInstalled()) {
            $sql = file_get_contents('install.sql');
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
