<?php
require_once('InstallSQLTrait.php');
require_once('SaveResultTrait.php');

class KnapsackAlgorithm {

    use InstallSQLTrait;
    use SaveResultTrait {
        setPdo as protected setPdoForSaveResultTrait;
    }

    private $pdo;
    private $table = 'products';
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->setPdo($pdo);
        $this->setPdoForSaveResultTrait($pdo);
    }
    
    public function findOneCollection()
    {
        $stmt = $this->pdo->prepare("CALL `get30products`(@products, @remainingPrice);");
        $stmt->execute();
        $stmt->closeCursor();

        $result1 = $this->pdo
            ->query("SELECT @products AS `str`, @remainingPrice AS `remainingPrice`;")
            ->fetch(PDO::FETCH_ASSOC);
        $remainingPrice = $result1['remainingPrice'];
        $stmt = $this->pdo->prepare("CALL `findProductWithExactPrice`(?, @productId);");
        $stmt->execute([$remainingPrice]);
        $result2 = $this->pdo
            ->query("SELECT @productId AS `productId`;")
            ->fetch(PDO::FETCH_ASSOC);
        if($result2['productId'] == 0) {
            throw new Exception('Not found');
        }
        return $result1['str'] . ',' . $result2['productId'];
    }
    
    public function findMultipleCollections($total = 10)
    {
        $collections = [];
        for($i = 0; $i < $total; $i++) {
            try {
                $collections[] = $this->findOneCollection();
            }
            catch(Exception $e) {
            }
        }
        return $collections;
    }
    
    public function findAndSaveCollections($total = 10)
    {
        $collections = $this->findMultipleCollections($total);
        foreach($collections as $collection) {
            $items = explode(',', $collection);
            $this->insertToResults($collection, count($items), 500);
        }
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
