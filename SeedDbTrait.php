<?php

trait SeedDbTrait {

    private $pdo;
    
    protected function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
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
