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
        $stmt = $pdo->prepare("CALL `get30products`(@p0, @p1); SELECT @p0 AS `str`, @p1 AS `remainingPrice`;");
        $stmt->execute();
        $result1 = $stmt->fetch(PDO::FETCH_ASSOC);
        $remainingPrice = $result1['remainingPrice'];
        $stmt = $pdo->prepare("CALL `findProductWithExactPrice`(?, @p1); SELECT @p1 AS `productId`;");
        $stmt->execute([$remainingPrice]);
        $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result['productId'] == 0) {
            throw new Exception('Not found');
        }
        return $result1['str'] . ',' . $result2['productId'];
    }
    
    public function findMultipleCollections($total)
    {
    }
    
    public function findAndSaveCollections()
    {
    }
}
