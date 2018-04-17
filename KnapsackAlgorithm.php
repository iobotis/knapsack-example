<?php

class KnapsackAlgorithm {

    private $pdo;
    private $table = 'products';
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function install()
    {
        if(!$this->isInstalled()) {
            $sql = file_get_contents('file.sql');
            $this->pdo->exec($sql);
        }
    }
    
    public function isInstalled()
    {
        $table = $this->table;
        try {
            $result = $this->pdo->query("SELECT 1 FROM $table LIMIT 1");
            return true;
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        }
        return false;
    }
    
    public function findOneCollection()
    {
    }
    
    public function findMultipleCollections($total)
    {
    }
    
    public function findAndSaveCollections()
    {
    }
}
